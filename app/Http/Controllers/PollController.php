<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use App\Support\PollCode;
use App\Models\Vote;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PollController - Controller xử lý các thao tác liên quan đến Poll (Khảo sát)
 * 
 * Controller này quản lý toàn bộ vòng đời của một Poll:
 * - Tạo mới Poll (với các loại: standard, ranking, image)
 * - Hiển thị form vote và kết quả
 * - Quản lý access key cho private polls
 * - Export dữ liệu ra CSV
 * - Bình luận và xóa Poll
 * 
 * @author QuickPoll Team
 */
class PollController extends Controller
{
    /**
     * Hiển thị form tạo Poll mới
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('polls.create');
    }

    /**
     * Lưu Poll mới vào database
     * 
     * Xử lý tạo Poll với các loại khác nhau:
     * - Standard: Poll thông thường (single/multiple choice)
     * - Ranking: Poll xếp hạng (user phải xếp hạng tất cả options)
     * - Image: Poll với hình ảnh (luôn multiple choice)
     * 
     * Logic đặc biệt:
     * - Tự động generate access key nếu poll là private mà không có key
     * - Map choice_type (single/multiple) thành allow_multiple boolean
     * - Map voting_security thành is_private boolean
     * - Reuse max_image_selections cho cả image poll và standard multiple choice
     * 
     * @param Request $request - Form data từ frontend
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'description_media' => ['nullable', 'array'],
            'description_media.*' => ['nullable', 'string', 'max:500'],
            'poll_type' => ['required', 'in:standard,ranking,image'],
            'choice_type' => ['required', 'in:single,multiple'],
            'max_choices' => ['nullable', 'integer', 'min:2'],
            'max_image_selections' => ['nullable', 'integer', 'min:1'],
            'allow_multiple' => ['nullable', 'boolean'],
            'options' => ['required', 'array', 'min:2'],
            'options.*' => ['required', 'string', 'min:1', 'max:100'],
            'image_urls' => ['nullable', 'array'],
            'image_urls.*' => ['nullable', 'max:500'],
            'image_titles' => ['nullable', 'array'],
            'image_titles.*' => ['nullable', 'string', 'max:100'],
            'image_option_texts' => ['nullable', 'array'],
            'image_option_texts.*' => ['nullable', 'string', 'max:100'],
            'is_private' => ['nullable', 'boolean'],
            'access_key' => ['nullable', 'string', 'min:6', 'max:64'],
            'voting_security' => ['nullable', 'in:session,private'],
            'auto_close_enabled' => ['nullable', 'in:0,1'],
            'auto_close_at' => ['nullable', 'date'],
            'allow_comments' => ['nullable', 'in:0,1'],
            'hide_share' => ['nullable', 'in:0,1'],
        ]);

        // Skip custom validation for image URLs - let Laravel handle it
        // The frontend will clean up empty URLs before submission

        /**
         * Map choice_type (single/multiple) từ frontend thành allow_multiple boolean
         * Frontend gửi 'single' hoặc 'multiple', backend cần boolean
         */
        $allowMultiple = ($validated['choice_type'] ?? 'single') === 'multiple';
        
        /**
         * Map voting_security (session/private) thành is_private boolean
         * 'private' = poll cần access key, 'session' = poll công khai
         */
        $isPrivate = ($validated['voting_security'] ?? 'session') === 'private';
        
        /**
         * Tự động generate access key cho private polls nếu user không nhập
         * Key có 8 ký tự, dùng MD5 hash của unique ID
         */
        $accessKey = $validated['access_key'] ?? null;
        if ($isPrivate && empty($accessKey)) {
            $accessKey = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        }

        /**
         * Image polls luôn là multiple choice (user có thể chọn nhiều hình)
         * Không cho phép single choice cho image polls
         */
        if ($validated['poll_type'] === 'image') {
            $allowMultiple = true;
        }

        /**
         * Xử lý max selections limit:
         * - Image polls: dùng max_image_selections từ form
         * - Standard multiple choice: dùng max_choices từ form, lưu vào max_image_selections
         * 
         * Lý do reuse field: Tiết kiệm database column, tránh migration không cần thiết
         */
        $maxImageSelections = null;
        if ($validated['poll_type'] === 'image') {
            $maxImageSelections = $validated['max_image_selections'] ?? null;
        } elseif ($validated['poll_type'] === 'standard' && $allowMultiple) {
            // For standard multiple choice polls, use max_choices as max_image_selections
            $maxImageSelections = $validated['max_choices'] ?? null;
        }

        $poll = Poll::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'description_media' => $validated['description_media'] ?? null,
            'question' => $validated['title'], // Keep question field for backward compatibility
            'slug' => PollCode::generateUniqueSlug(),
            'poll_type' => $validated['poll_type'],
            'max_image_selections' => $maxImageSelections,
            'allow_multiple' => $allowMultiple,
            'is_private' => $isPrivate,
            'access_key' => $accessKey,
            'voting_security' => $validated['voting_security'] ?? 'session',
            'auto_close_at' => ($validated['auto_close_enabled'] ?? '0') === '1' ? ($validated['auto_close_at'] ?? null) : null,
            'allow_comments' => ($validated['allow_comments'] ?? '0') === '1',
            'hide_share' => ($validated['hide_share'] ?? '0') === '1',
        ]);

        /**
         * Tạo Poll Options dựa trên loại Poll:
         * - Image polls: Tạo options với image_url, image_title, image_alt_text
         * - Standard/Ranking polls: Chỉ tạo options với option_text (text only)
         */
        if ($validated['poll_type'] === 'image') {
            // Image polls: Lấy data từ 3 arrays song song (image_urls, image_titles, image_option_texts)
            $imageUrls = $validated['image_urls'] ?? [];
            $imageTitles = $validated['image_titles'] ?? [];
            $imageOptionTexts = $validated['image_option_texts'] ?? [];
            
            $optionData = [];
            // Loop qua tất cả image_option_texts để tạo options
            for ($i = 0; $i < count($imageOptionTexts); $i++) {
                $imageUrl = $imageUrls[$i] ?? null;
                $imageTitle = $imageTitles[$i] ?? null;
                $optionText = $imageOptionTexts[$i] ?? '';
                
                // Chỉ lưu image data nếu có URL hợp lệ
                // Nếu không có image_title, dùng option_text làm alt_text
                $optionData[] = [
                    'option_text' => $optionText,
                    'image_url' => !empty($imageUrl) ? $imageUrl : null,
                    'image_title' => !empty($imageTitle) ? $imageTitle : null,
                    'image_alt_text' => !empty($imageTitle) ? $imageTitle : $optionText,
                ];
            }
            
            $poll->options()->createMany($optionData);
        } else {
            // Standard/Ranking polls: Chỉ cần option_text
            // Filter bỏ các option rỗng trước khi lưu
            $options = collect($validated['options'])
                ->filter(fn ($text) => trim($text) !== '')
                ->map(fn ($text) => ['option_text' => $text]);

            $poll->options()->createMany($options->all());
        }

        return redirect()->route('dashboard')->with('success', __('messages.poll_created_successfully'));
    }

    /**
     * Hiển thị trang vote cho một Poll
     * 
     * Xử lý:
     * - Tự động đóng Poll nếu đã qua thời gian auto_close_at
     * - Kiểm tra xem user đã vote chưa (dựa trên voter_identifier)
     * - Yêu cầu guest nhập tên trước khi vote (nếu chưa có)
     * - Load comments nếu Poll cho phép bình luận
     * 
     * @param string $slug - Slug của Poll (unique identifier)
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function vote(string $slug)
    {
        // Eager load options với vote count để hiển thị kết quả real-time
        $poll = Poll::with(['options' => function ($q) {
            $q->withCount('votes');
        }])->where('slug', $slug)->firstOrFail();

        /**
         * Tự động đóng Poll nếu đã qua thời gian auto_close_at
         * Check mỗi lần load trang để đảm bảo poll được đóng đúng thời điểm
         */
        if ($poll->auto_close_at && now()->greaterThanOrEqualTo($poll->auto_close_at)) {
            $poll->is_closed = true;
            $poll->save();
        }

        /**
         * Kiểm tra user đã vote chưa:
         * - Logged in user: dùng "user_{id}" làm identifier
         * - Guest: dùng "session_{session_id}" làm identifier
         * 
         * Cả owner và guest đều không thể vote lại (prevent duplicate votes)
         */
        $sessionId = request()->session()->getId();
        $isOwner = Auth::check() && Auth::id() === $poll->user_id;
        $voterIdentifier = Auth::check() ? "user_" . Auth::id() : "session_" . $sessionId;
        $hasVoted = Vote::where('poll_id', $poll->id)
            ->where('voter_identifier', $voterIdentifier)
            ->exists();

        /**
         * Yêu cầu guest nhập tên trước khi vote (chỉ cho private polls hoặc polls yêu cầu)
         * Owner và logged-in users không cần nhập tên (đã có sẵn từ account)
         */
        if (!$isOwner && !Auth::check() && !$hasVoted) {
            if (!session()->has('voter_name') || trim((string)session('voter_name')) === '') {
                return redirect()->route('polls.name', $poll->slug);
            }
        }

        /**
         * Load comments nếu Poll cho phép bình luận
         * Comments được load với user info để hiển thị tên người comment
         */
        $comments = collect();
        if ($poll->allow_comments) {
            $comments = Comment::where('poll_id', $poll->id)
                ->with('user')
                ->latest()
                ->get();
        }

        return view('polls.vote', [
            'poll' => $poll,
            'hasVoted' => $hasVoted,
            'isOwner' => $isOwner,
            'comments' => $comments,
        ]);
    }

    /**
     * Hiển thị form nhập tên cho guest user
     * 
     * Form này xuất hiện khi guest chưa có tên trong session
     * (Thường cho private polls yêu cầu biết người vote)
     * 
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function nameForm(string $slug)
    {
        $poll = Poll::where('slug', $slug)->firstOrFail();
        return view('polls.name', ['slug' => $slug, 'poll' => $poll]);
    }

    /**
     * Lưu tên guest vào session và redirect về trang vote
     * 
     * Tên được lưu vào session và sẽ được dùng làm voter_name khi tạo Vote
     * 
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveName(Request $request, string $slug)
    {
        $request->validate(['voter_name' => ['required','string','max:100']]);
        $request->session()->put('voter_name', $request->input('voter_name'));
        return redirect()->route('polls.vote', $slug);
    }

    /**
     * Hiển thị trang kết quả Poll (Results page)
     * 
     * Trang này hiển thị:
     * - Tổng số votes/participants
     * - Biểu đồ phân bố votes theo từng option
     * - Top option được vote nhiều nhất
     * - Time series chart (chỉ owner mới thấy)
     * - Danh sách voters với search/filter (chỉ owner)
     * - Comments (nếu enabled)
     * 
     * Đặc biệt cho Ranking polls:
     * - Tính điểm Borda Count (rank-based scoring)
     * - Hiển thị participants count thay vì total votes
     * 
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show(Request $request, string $slug)
    {
        /**
         * Poll có thể được load từ middleware (EnsurePollAccess) nếu là private poll
         * Nếu không, load trực tiếp từ database
         */
        $poll = $request->attributes->get('poll');
        if (!$poll) {
            $poll = Poll::with('options')->where('slug', $slug)->firstOrFail();
        } else {
            $poll->load('options'); // Load options if not already loaded
        }

        // Auto-close enforcement (giống như trong vote())
        if ($poll->auto_close_at && now()->greaterThanOrEqualTo($poll->auto_close_at)) {
            $poll->is_closed = true;
            $poll->save();
        }

        $isOwner = Auth::check() && Auth::id() === $poll->user_id;
        
        // Debug info
        Log::info('Poll Debug', [
            'poll_id' => $poll->id,
            'hide_share' => $poll->hide_share,
            'hide_share_type' => gettype($poll->hide_share),
            'isOwner' => $isOwner,
            'auth_check' => Auth::check(),
            'auth_id' => Auth::id(),
            'poll_user_id' => $poll->user_id
        ]);

        /**
         * Tính toán thống kê:
         * - totalVotes: Tổng số votes (bao gồm cả multiple choice)
         * - totalParticipants: Số người tham gia unique (dùng cho ranking polls)
         * 
         * Lý do phân biệt:
         * - Ranking polls: 1 user vote = 1 participant (dù vote nhiều options)
         * - Regular polls: 1 user có thể vote nhiều options (multiple choice)
         * -> Để chính xác, dùng distinct voter_identifier cho cả 2 loại
         */
        $totalVotes = Vote::where('poll_id', $poll->id)->count();
        
        // Đếm unique participants (dùng cho ranking polls và hiển thị chính xác)
        $totalParticipants = Vote::where('poll_id', $poll->id)
            ->distinct('voter_identifier')
            ->count('voter_identifier');
        
        $topOption = null;
        if ($poll->options->isNotEmpty()) {
            $topOption = $poll->options
                ->map(function ($opt) {
                    $opt->computed_votes = $opt->votes()->count();
                    return $opt;
                })
                ->sortByDesc('computed_votes')
                ->first();
        }

        $series = [];
        if ($isOwner) {
            $series = Vote::selectRaw("DATE(created_at) as d, COUNT(*) as c")
                ->where('poll_id', $poll->id)
                ->groupBy('d')
                ->orderBy('d')
                ->get();
        }

        /**
         * Danh sách voters với search và filter (chỉ owner mới thấy)
         * 
         * Features:
         * - Search theo voter_name hoặc option_text
         * - Filter theo date range (from/to)
         * - Pagination (10 items/page)
         * - Sort theo created_at (newest first)
         */
        $voters = collect();
        $votersPaginator = null;
        if ($isOwner) {
            $query = Vote::where('poll_id', $poll->id)->with('option');
            
            // Search: Tìm theo tên người vote hoặc text của option
            $search = request('q');
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('voter_name', 'like', '%'.$search.'%')
                      ->orWhereHas('option', function($qq) use ($search) {
                          $qq->where('option_text', 'like', '%'.$search.'%');
                      });
                });
            }
            
            // Filter theo date range
            if ($from = request('from')) {
                $query->where('created_at', '>=', $from.' 00:00:00');
            }
            if ($to = request('to')) {
                $query->where('created_at', '<=', $to.' 23:59:59');
            }
            
            // Pagination với query string preservation (giữ filter/search khi chuyển trang)
            $votersPaginator = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
            $voters = $votersPaginator->getCollection();
        }

        // Comments
        $comments = [];
        if ($poll->allow_comments) {
            $comments = $poll->comments()->latest()->limit(100)->get();
        }

        return view('polls.show', [
            'poll' => $poll,
            'totalVotes' => $totalVotes,
            'totalParticipants' => $totalParticipants,
            'topOption' => $topOption,
            'series' => $series,
            'voters' => $voters,
            'votersPaginator' => $votersPaginator,
            'isOwner' => $isOwner,
            'comments' => $comments,
        ]);
    }

    public function toggle(string $slug)
    {
        $poll = Poll::where('slug', $slug)->where('user_id', Auth::id())->firstOrFail();
        $poll->is_closed = !$poll->is_closed;
        $poll->save();
        return back()->with('success', $poll->is_closed ? __('messages.poll_closed') : __('messages.poll_opened'));
    }

    public function exportCsv(string $slug)
    {
        $poll = Poll::with(['options.votes', 'votes.option', 'comments.user'])->where('slug', $slug)->firstOrFail();
        if ($poll->user_id !== Auth::id()) {
            abort(403);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="poll_'.$poll->slug.'_'.date('Y-m-d_H-i-s').'.csv"',
        ];

        $callback = function () use ($poll) {
            $out = fopen('php://output', 'w');
            
            // BOM for UTF-8 support in Excel
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Use semicolon as delimiter for locales that expect ; in CSV (e.g., vi-VN)
            $put = function(array $row) use ($out) {
                fputcsv($out, $row, ';');
            };
            
            // Poll overview information
            $put(['=== POLL INFORMATION ===']);
            $put(['Poll ID', $poll->id]);
            $put(['Poll Slug', $poll->slug]);
            $put(['Title', $poll->title ?? $poll->question]);
            $put(['Question', $poll->question]);
            $put(['Description', $poll->description ?? '(No description)']);
            $pollTypeText = match($poll->poll_type) {
                'ranking' => 'Ranking',
                'image' => 'Image',
                default => 'Regular'
            };
            $put(['Poll Type', $pollTypeText]);
            $put(['Allow Multiple', $poll->allow_multiple ? 'Yes' : 'No']);
            $put(['Voting Security', $poll->voting_security ?? 'Session-based']);
            $put(['Is Private', $poll->is_private ? 'Yes' : 'No']);
            if ($poll->is_private) {
                $put(['Access Key', $poll->access_key ?? 'Not set']);
            }
            $put(['Status', $poll->is_closed ? 'Closed' : 'Open']);
            $put(['Created At', $poll->created_at->format('Y-m-d H:i:s')]);
            $put(['Last Updated', $poll->updated_at->format('Y-m-d H:i:s')]);
            if ($poll->auto_close_at) {
                $put(['Auto Close Date', $poll->auto_close_at->format('Y-m-d H:i:s')]);
            } else {
                $put(['Auto Close Date', 'Not set']);
            }
            $put(['Total Votes', $poll->votes->count()]);
            $put(['Options Count', $poll->options->count()]);
            if ($poll->poll_type === 'image' && $poll->max_image_selections) {
                $put(['Max Image Selections', $poll->max_image_selections]);
            }
            $put(['Allow Comments', $poll->allow_comments ? 'Yes' : 'No']);
            $put(['Hide Share Button', $poll->hide_share ? 'Yes' : 'No']);
            $put([]); // Empty row
            
            // Poll Options Details
            $put(['=== POLL OPTIONS ===']);
            if ($poll->poll_type === 'image') {
                $put(['Option ID', 'Option Text/Title', 'Image URL', 'Image Alt Text', 'Vote Count']);
            } else {
                $put(['Option ID', 'Option Text', 'Vote Count']);
            }
            
            foreach ($poll->options as $option) {
                if ($poll->poll_type === 'image') {
                    $put([
                        $option->id,
                        $option->getDisplayText(),
                        $option->image_url ?? '',
                        $option->image_alt_text ?? '',
                        $option->votes->count()
                    ]);
                } else {
                    $put([
                        $option->id,
                        $option->option_text,
                        $option->votes->count()
                    ]);
                }
            }
            $put([]); // Empty row
            
            if ($poll->poll_type === 'ranking') {
                // Ranking poll results
                $put(['=== RANKING RESULTS ===']);
                
                // Calculate Borda Count scores
                $totalOptions = $poll->options->count();
                $rankings = $poll->options->mapWithKeys(function($option) use ($totalOptions) {
                    $votes = $option->votes;
                    $totalScore = 0;
                    foreach ($votes as $vote) {
                        $totalScore += $totalOptions - $vote->rank + 1;
                    }
                    return [$option->id => $totalScore];
                });
                
                $sortedOptions = $poll->options->sortByDesc(function($option) use ($rankings) {
                    return $rankings[$option->id] ?? 0;
                });
                
                $put(['Rank', 'Option ID', 'Option Text', 'Score', 'Vote Count']);
                $rank = 1;
                foreach ($sortedOptions as $option) {
                    $score = $rankings[$option->id] ?? 0;
                    $voteCount = $option->votes->count();
                    $put([$rank, $option->id, $option->option_text, $score, $voteCount]);
                    $rank++;
                }
                $put([]); // Empty row
                
                // Individual voter details
                $put(['=== INDIVIDUAL VOTER DETAILS (RANKING) ===']);
                $maxRanks = min(10, $poll->options->count());
                $headerRow = ['Vote Time', 'Voter Name', 'Voter Identifier'];
                for ($i = 1; $i <= $maxRanks; $i++) {
                    $headerRow[] = 'Rank ' . $i;
                }
                $put($headerRow);
                
                // Group votes by voter_identifier
                $groupedVotes = $poll->votes->groupBy('voter_identifier');
                foreach ($groupedVotes as $voterId => $votes) {
                    $firstVote = $votes->first();
                    $sortedVotes = $votes->sortBy('rank');
                    
                    $row = [
                        $firstVote->created_at->format('Y-m-d H:i:s'),
                        $firstVote->voter_name ?? '(Anonymous)',
                        $voterId ?? ''
                    ];
                    
                    // Create ranking array
                    $ranking = [];
                    foreach ($sortedVotes as $vote) {
                        $ranking[$vote->rank] = $vote->option->getDisplayText();
                    }
                    
                    // Fill rank columns
                    for ($i = 1; $i <= $maxRanks; $i++) {
                        $row[] = $ranking[$i] ?? '';
                    }
                    
                    $put($row);
                }
            } else {
                // Regular/Image poll results
                $put(['=== POLL RESULTS SUMMARY ===']);
                $put(['Option', 'Vote Count', 'Percentage']);
                
                $totalVotes = $poll->votes->count();
                foreach ($poll->options as $option) {
                    $voteCount = $option->votes->count();
                    $percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 2) : 0;
                    $put([$option->getDisplayText(), $voteCount, $percentage . '%']);
                }
                $put([]); // Empty row
                
                // Individual vote details
                $put(['=== INDIVIDUAL VOTE DETAILS ===']);
                if ($poll->poll_type === 'image') {
                    $put(['Vote ID', 'Vote Time', 'Voter Name', 'Option ID', 'Option Text/Title', 'IP Address', 'Session ID', 'Voter Identifier']);
                } else {
                    $put(['Vote ID', 'Vote Time', 'Voter Name', 'Option ID', 'Option Text', 'IP Address', 'Session ID', 'Voter Identifier']);
                }
                
                foreach ($poll->votes->sortByDesc('created_at') as $vote) {
                    $put([
                        $vote->id,
                        $vote->created_at->format('Y-m-d H:i:s'),
                        $vote->voter_name ?? '(Anonymous)',
                        $vote->option_id,
                        $vote->option->getDisplayText(),
                        $vote->ip_address ?? '',
                        $vote->session_id ?? '',
                        $vote->voter_identifier ?? ''
                    ]);
                }
            }
            
            // Comments section if enabled
            if ($poll->allow_comments) {
                $put([]); // Empty row
                $put(['=== COMMENTS ===']);
                $put(['Comment ID', 'Comment Time', 'User/Voter Name', 'Comment Content']);
                
                foreach ($poll->comments->sortByDesc('created_at') as $comment) {
                    $put([
                        $comment->id,
                        $comment->created_at->format('Y-m-d H:i:s'),
                        $comment->user ? $comment->user->name : ($comment->voter_name ?? '(Anonymous)'),
                        $comment->content
                    ]);
                }
            }
            
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function accessForm(string $slug)
    {
        $poll = Poll::where('slug', $slug)->firstOrFail();
        return view('polls.access', ['slug' => $slug, 'poll' => $poll]);
    }

    public function accessCheck(Request $request, string $slug)
    {
        $poll = Poll::where('slug', $slug)->firstOrFail();
        $request->validate(['access_key' => ['required','string']]);
        $ok = ($poll->access_key && hash_equals($poll->access_key, $request->input('access_key')))
            || (!$poll->access_key && $request->input('access_key') === '');
        if (!$ok) {
            return back()->with('error', __('messages.invalid_access_key'));
        }
        session(['poll_access_'.$poll->id => true]);
            return redirect()->route('polls.vote', $poll->slug)->with('success', __('messages.access_granted'));
    }

    public function quickAccess(string $code)
    {
        // Find poll by slug (using code as slug)
        $poll = Poll::where('slug', $code)->first();
        
        if (!$poll) {
            return redirect()->route('dashboard')->with('error', 'Poll not found with the provided code.');
        }
        
        // If poll is private, redirect to access form
        if ($poll->is_private) {
            return redirect()->route('polls.access', $poll->slug);
        }
        
        // If poll is public, redirect directly to vote
        return redirect()->route('polls.vote', $poll->slug);
    }

    public function comment(Request $request, string $slug)
    {
        $poll = Poll::where('slug', $slug)->firstOrFail();
        if (!$poll->allow_comments) {
            abort(403);
        }
        $validated = $request->validate([
            'content' => ['required','string','min:1','max:1000'],
            'voter_name' => ['nullable','string','max:100'],
        ]);
        \App\Models\Comment::create([
            'poll_id' => $poll->id,
            'voter_name' => $validated['voter_name'] ?? $request->session()->get('voter_name'),
            'content' => $validated['content'],
            'session_id' => $request->session()->getId(),
        ]);
        return back();
    }

    public function destroy(string $slug)
    {
        $poll = Poll::where('slug', $slug)->firstOrFail();
        if ($poll->user_id !== Auth::id()) {
            abort(403);
        }
        $poll->delete();
        return redirect()->route('dashboard')->with('success', __('messages.poll_deleted'));
    }
}