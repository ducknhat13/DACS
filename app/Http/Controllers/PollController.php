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

class PollController extends Controller
{
    public function create()
    {
        return view('polls.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'min:5', 'max:500'],
            'poll_type' => ['required', 'in:regular,ranking'],
            'allow_multiple' => ['nullable', 'boolean'],
            'options' => ['required', 'array', 'min:2'],
            'options.*' => ['required', 'string', 'min:1', 'max:100'],
            'is_private' => ['nullable', 'boolean'],
            'access_key' => ['nullable', 'string', 'min:6', 'max:64'],
            'voting_security' => ['nullable', 'in:session,private'],
            'auto_close_enabled' => ['nullable', 'in:0,1'],
            'auto_close_at' => ['nullable', 'date'],
            'allow_comments' => ['nullable', 'in:0,1'],
            'hide_share' => ['nullable', 'in:0,1'],
        ]);

        // Map voting_security to is_private
        $isPrivate = ($validated['voting_security'] ?? 'session') === 'private';
        
        // Auto-generate access key for private polls if not provided
        $accessKey = $validated['access_key'] ?? null;
        if ($isPrivate && empty($accessKey)) {
            $accessKey = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        }

        $poll = Poll::create([
            'user_id' => Auth::id(),
            'question' => $validated['question'],
            'slug' => PollCode::generateUniqueSlug(),
            'poll_type' => $validated['poll_type'],
            'allow_multiple' => (bool)($validated['allow_multiple'] ?? false),
            'is_private' => $isPrivate,
            'access_key' => $accessKey,
            'voting_security' => $validated['voting_security'] ?? 'session',
            'auto_close_at' => ($validated['auto_close_enabled'] ?? '0') === '1' ? ($validated['auto_close_at'] ?? null) : null,
            'allow_comments' => ($validated['allow_comments'] ?? '0') === '1',
            'hide_share' => ($validated['hide_share'] ?? '0') === '1',
        ]);

        $options = collect($validated['options'])
            ->filter(fn ($text) => trim($text) !== '')
            ->map(fn ($text) => ['option_text' => $text]);

        $poll->options()->createMany($options->all());

        return redirect()->route('dashboard')->with('success', __('messages.poll_created_successfully'));
    }

    public function vote(string $slug)
    {
        $poll = Poll::with(['options' => function ($q) {
            $q->withCount('votes');
        }])->where('slug', $slug)->firstOrFail();

        // Auto-close enforcement
        if ($poll->auto_close_at && now()->greaterThanOrEqualTo($poll->auto_close_at)) {
            $poll->is_closed = true;
            $poll->save();
        }

        // Check if user has voted
        $sessionId = request()->session()->getId();
        $isOwner = Auth::check() && Auth::id() === $poll->user_id;
        $voterIdentifier = Auth::check() ? "user_" . Auth::id() : "session_" . $sessionId;
        $hasVoted = Vote::where('poll_id', $poll->id)
            ->where('voter_identifier', $voterIdentifier)
            ->exists();

        // Enforce guest name before voting (only when not owner, not logged in and not yet voted)
        if (!$isOwner && !Auth::check() && !$hasVoted) {
            if (!session()->has('voter_name') || trim((string)session('voter_name')) === '') {
                return redirect()->route('polls.name', $poll->slug);
            }
        }

        // Get comments if enabled
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

    // Show guest name form
    public function nameForm(string $slug)
    {
        $poll = Poll::where('slug', $slug)->firstOrFail();
        return view('polls.name', ['slug' => $slug, 'poll' => $poll]);
    }

    // Save guest name and return to vote page
    public function saveName(Request $request, string $slug)
    {
        $request->validate(['voter_name' => ['required','string','max:100']]);
        $request->session()->put('voter_name', $request->input('voter_name'));
        return redirect()->route('polls.vote', $slug);
    }

    public function show(Request $request, string $slug)
    {
        // Get poll from middleware if available, otherwise load it
        $poll = $request->attributes->get('poll');
        if (!$poll) {
            $poll = Poll::with('options')->where('slug', $slug)->firstOrFail();
        } else {
            $poll->load('options'); // Load options if not already loaded
        }

        // Auto-close enforcement
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

        // Stats: total votes, top option, time series by day
        $totalVotes = Vote::where('poll_id', $poll->id)->count();
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

        $voters = collect();
        $votersPaginator = null;
        if ($isOwner) {
            $query = Vote::where('poll_id', $poll->id)->with('option');
            // Search by voter name or option text
            $search = request('q');
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('voter_name', 'like', '%'.$search.'%')
                      ->orWhereHas('option', function($qq) use ($search) {
                          $qq->where('option_text', 'like', '%'.$search.'%');
                      });
                });
            }
            // Filter by time range
            if ($from = request('from')) {
                $query->where('created_at', '>=', $from.' 00:00:00');
            }
            if ($to = request('to')) {
                $query->where('created_at', '<=', $to.' 23:59:59');
            }
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
        $poll = Poll::with(['options.votes', 'votes.option'])->where('slug', $slug)->firstOrFail();
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
            
            // Poll overview information
            fputcsv($out, ['=== POLL INFORMATION ===']);
            fputcsv($out, ['Poll ID', $poll->id]);
            fputcsv($out, ['Question', $poll->question]);
            fputcsv($out, ['Poll Type', $poll->poll_type === 'ranking' ? 'Ranking' : 'Regular']);
            fputcsv($out, ['Allow Multiple', $poll->allow_multiple ? 'Yes' : 'No']);
            fputcsv($out, ['Status', $poll->is_closed ? 'Closed' : 'Open']);
            fputcsv($out, ['Created At', $poll->created_at->format('d/m/Y H:i:s')]);
            fputcsv($out, ['Last Updated', $poll->updated_at->format('d/m/Y H:i:s')]);
            fputcsv($out, ['Total Votes', $poll->votes->count()]);
            fputcsv($out, ['Options Count', $poll->options->count()]);
            fputcsv($out, []); // Empty row
            
            if ($poll->poll_type === 'ranking') {
                // Ranking poll results
                fputcsv($out, ['=== RANKING RESULTS ===']);
                
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
                
                fputcsv($out, ['Rank', 'Option', 'Score', 'Vote Count']);
                $rank = 1;
                foreach ($sortedOptions as $option) {
                    $score = $rankings[$option->id] ?? 0;
                    $voteCount = $option->votes->count();
                    fputcsv($out, [$rank, $option->option_text, $score, $voteCount]);
                    $rank++;
                }
                fputcsv($out, []); // Empty row
                
                // Individual voter details
                fputcsv($out, ['=== INDIVIDUAL VOTER DETAILS (RANKING) ===']);
                fputcsv($out, ['Vote Time', 'Voter Name', 'Rank 1', 'Rank 2', 'Rank 3', 'Rank 4', 'Rank 5', 'Rank 6', 'Rank 7', 'Rank 8', 'Rank 9', 'Rank 10']);
                
                // Group votes by voter_identifier
                $groupedVotes = $poll->votes->groupBy('voter_identifier');
                foreach ($groupedVotes as $voterId => $votes) {
                    $firstVote = $votes->first();
                    $sortedVotes = $votes->sortBy('rank');
                    
                    $row = [
                        $firstVote->created_at->format('d/m/Y H:i:s'),
                        $firstVote->voter_name ?? '(Anonymous)'
                    ];
                    
                    // Create ranking array with max 10 ranks
                    $ranking = [];
                    foreach ($sortedVotes as $vote) {
                        $ranking[$vote->rank] = $vote->option->option_text;
                    }
                    
                    // Fill 10 rank columns
                    for ($i = 1; $i <= 10; $i++) {
                        $row[] = $ranking[$i] ?? '';
                    }
                    
                    fputcsv($out, $row);
                }
            } else {
                // Regular poll results
                fputcsv($out, ['=== REGULAR POLL RESULTS ===']);
                fputcsv($out, ['Option', 'Vote Count', 'Percentage']);
                
                $totalVotes = $poll->votes->count();
                foreach ($poll->options as $option) {
                    $voteCount = $option->votes->count();
                    $percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 2) : 0;
                    fputcsv($out, [$option->option_text, $voteCount, $percentage . '%']);
                }
                fputcsv($out, []); // Empty row
                
                // Individual vote details
                fputcsv($out, ['=== INDIVIDUAL VOTE DETAILS ===']);
                fputcsv($out, ['Vote Time', 'Voter Name', 'Option', 'IP Address', 'Session ID']);
                
                foreach ($poll->votes->sortByDesc('created_at') as $vote) {
                    fputcsv($out, [
                        $vote->created_at->format('d/m/Y H:i:s'),
                        $vote->voter_name ?? '(Anonymous)',
                        $vote->option->option_text,
                        $vote->ip_address,
                        $vote->session_id
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