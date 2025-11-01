<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\Vote;
use App\Notifications\NewVoteNotification;
// use App\Events\NewVoteCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * VoteController - Controller xử lý việc bình chọn (voting) cho Poll
 * 
 * Controller này xử lý:
 * - Lưu vote từ user (standard, ranking, image polls)
 * - Validate vote data (prevent duplicate votes, check limits)
 * - Xử lý "Other" option cho standard polls
 * - Gửi email notification cho poll owner khi có vote mới
 * 
 * Đặc biệt:
 * - Ranking polls: Tạo nhiều Vote records (1 record cho mỗi option với rank)
 * - Regular polls: Tạo 1 hoặc nhiều Vote records (tùy single/multiple choice)
 * - Sử dụng DB transaction để đảm bảo data consistency
 * 
 * @author QuickPoll Team
 */
class VoteController extends Controller
{
    /**
     * Xử lý submit vote từ form
     * 
     * Flow:
     * 1. Load poll với options và owner (để gửi notification)
     * 2. Kiểm tra poll đã đóng chưa
     * 3. Tạo voter_identifier (user_id hoặc session_id)
     * 4. Kiểm tra user đã vote chưa (prevent duplicate)
     * 5. Route đến handler phù hợp (ranking hoặc regular)
     * 
     * @param Request $request - Form data (options[], ranking[], other_option)
     * @param string $slug - Poll slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, string $slug)
    {
        // Eager load options (để validate) và user (để gửi notification)
        $poll = Poll::with(["options", "user"])->where("slug", $slug)->firstOrFail();
        
        // Kiểm tra poll đã đóng chưa
        if ($poll->is_closed) {
            return back()->with("error", __("messages.poll_is_closed"));
        }

        /**
         * Tạo voter_identifier để tracking:
         * - Logged in: "user_{id}" (persistent across sessions)
         * - Guest: "session_{session_id}" (reset khi clear cookies)
         */
        $sessionId = $request->session()->getId();
        $voterIdentifier = Auth::check() ? "user_" . Auth::id() : "session_" . $sessionId;
        
        /**
         * Kiểm tra user đã vote chưa (dựa trên voter_identifier)
         * Cả owner và guest đều không thể vote lại (prevent duplicate)
         */
        $isOwner = Auth::check() && Auth::id() === $poll->user_id;
        $existingVote = Vote::where("poll_id", $poll->id)
            ->where("voter_identifier", $voterIdentifier)
            ->exists();
            
        if ($existingVote) {
            return back()->with("error", __("messages.you_have_voted"));
        }

        /**
         * Route đến handler phù hợp:
         * - Ranking polls: handleRankingVote() (xử lý ranking array)
         * - Standard/Image polls: handleRegularVote() (xử lý selected options)
         */
        if ($poll->poll_type === "ranking") {
            return $this->handleRankingVote($request, $poll, $voterIdentifier);
        } else {
            return $this->handleRegularVote($request, $poll, $voterIdentifier);
        }
    }

    /**
     * Xử lý vote cho Ranking polls
     * 
     * Ranking polls yêu cầu user phải xếp hạng TẤT CẢ options
     * Mỗi option sẽ có 1 Vote record với field "rank" (1 = best, n = worst)
     * 
     * Validation:
     * - Phải có ranking data (dạng array hoặc JSON string)
     * - Phải rank tất cả options (không được bỏ sót)
     * 
     * @param Request $request
     * @param Poll $poll
     * @param string $voterIdentifier
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleRankingVote(Request $request, Poll $poll, string $voterIdentifier)
    {
        /**
         * Ranking data có thể là:
         * - Array: {option_id: rank, ...} (từ form)
         * - JSON string: Cần decode thành array (từ hidden input)
         */
        $ranking = $request->input("ranking", []);
        if (is_string($ranking)) {
            $decoded = json_decode($ranking, true);
            $ranking = is_array($decoded) ? $decoded : [];
        }
        
        // Validate: Phải có ranking data
        if (empty($ranking) || !is_array($ranking)) {
            return back()->with("error", __("messages.pls_rank"));
        }

        /**
         * Validate: Phải rank TẤT CẢ options
         * - Lấy danh sách option IDs từ poll
         * - So sánh với keys trong ranking array
         * - Phải khớp số lượng và tất cả options đều có trong ranking
         */
        $optionIds = $poll->options->pluck("id")->toArray();
        $rankingOptionIds = array_keys($ranking);
        
        // Check 1: Số lượng phải khớp
        if (count($rankingOptionIds) !== count($optionIds)) {
            return back()->with("error", __("messages.pls_rank"));
        }
        
        // Check 2: Tất cả options đều có trong ranking
        foreach ($optionIds as $optionId) {
            if (!isset($ranking[$optionId])) {
                return back()->with("error", __("messages.pls_rank"));
            }
        }

        /**
         * Tạo Vote records trong DB transaction
         * - Mỗi option = 1 Vote record với rank
         * - Lưu voter_name (từ account hoặc session)
         * - Gửi notification cho owner (chỉ 1 lần, dùng firstVote)
         */
        DB::transaction(function () use ($poll, $voterIdentifier, $ranking, $request) {
            /**
             * Lấy display name:
             * - Logged in user: Lấy từ Auth::user()->name
             * - Guest: Lấy từ session ('voter_name')
             */
            $displayName = Auth::check() ? (Auth::user()->name ?? null) : null;
            if (!$displayName) {
                $displayName = $request->session()->get("voter_name");
            }
            
            /**
             * Tạo Vote record cho mỗi option trong ranking
             * firstVote dùng để gửi notification (chỉ gửi 1 lần, không phải mỗi option)
             */
            $firstVote = null;
            foreach ($ranking as $optionId => $rank) {
                $vote = Vote::create([
                    "poll_id" => $poll->id,
                    "poll_option_id" => $optionId,
                    "user_id" => Auth::id(), // null nếu guest
                    "ip_address" => $request->ip(),
                    "session_id" => $request->session()->getId(),
                    "voter_identifier" => $voterIdentifier,
                    "voter_name" => $displayName,
                    "rank" => (int) $rank, // 1 = best, n = worst
                ]);
                if ($firstVote === null) {
                    $firstVote = $vote;
                }
            }
            
            /**
             * Gửi email notification cho poll owner nếu enabled
             * - Chỉ gửi 1 lần (dùng firstVote), không gửi cho mỗi option
             * - Local env: notifyNow() (sync, để test với Mailpit)
             * - Production: notify() (queued, async)
             */
            if ($firstVote && $poll->user && $poll->user->email_on_vote) {
                if (app()->environment('local')) {
                    $poll->user->notifyNow(new NewVoteNotification($poll->fresh(), $firstVote));
                } else {
                    $poll->user->notify(new NewVoteNotification($poll->fresh(), $firstVote));
                }
            }
        });

        $request->session()->put("poll_voted_" . $poll->id, true);
        return redirect()->route("polls.vote", $poll->slug)->with("success", __("messages.thank_you"));
    }

    /**
     * Xử lý vote cho Standard và Image polls
     * 
     * Xử lý:
     * - Single choice: Chỉ 1 option được chọn
     * - Multiple choice: Nhiều options được chọn (có thể có limit)
     * - "Other" option: User có thể nhập text tự do
     * 
     * Validation:
     * - Phải chọn ít nhất 1 option (hoặc nhập "Other")
     * - Single choice: Không được chọn > 1 option
     * - Multiple choice: Kiểm tra max selections limit
     * 
     * @param Request $request
     * @param Poll $poll
     * @param string $voterIdentifier
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleRegularVote(Request $request, Poll $poll, string $voterIdentifier)
    {
        /**
         * Lấy selected options và "Other" text input
         * Frontend gửi "__other__" placeholder nếu user chọn "Other"
         */
        $selectedOptions = $request->input("options", []);
        $otherTextInput = trim((string)$request->input('other_option'));
        
        // Kiểm tra poll có option "Other" không (case-insensitive)
        $hasOtherOption = $poll->options->contains(function($opt){
            return strtolower(trim($opt->option_text)) === 'other';
        });
        
        // Ensure selectedOptions luôn là array (phòng trường hợp single choice gửi string)
        if (!is_array($selectedOptions)) {
            $selectedOptions = [$selectedOptions];
        }
        
        /**
         * Tách valid options và "__other__" placeholder
         * "__other__" không phải là option ID thật, chỉ là marker
         */
        $validSelectedOptions = array_filter($selectedOptions, function($optionId) {
            return $optionId !== '__other__';
        });
        
        /**
         * Validation: Nếu user chọn "Other" nhưng không nhập text
         */
        $hasOtherSelected = in_array('__other__', $selectedOptions);
        if ($hasOtherSelected && $otherTextInput === '') {
            return back()->with("error", __("messages.pls_enter_other_text"));
        }
        
        /**
         * Validation: Phải chọn ít nhất 1 option (hoặc nhập "Other" text)
         */
        if (empty($validSelectedOptions) && !($hasOtherOption && $otherTextInput !== '')) {
            return back()->with("error", __("messages.pls_select"));
        }

        /**
         * Validation: Single choice polls không được chọn > 1 option
         */
        if (!$poll->allow_multiple && count($validSelectedOptions) > 1) {
            return back()->with("error", __("messages.only_one"));
        }

        /**
         * Validation: Kiểm tra max selections limit cho multiple choice polls
         * - max_image_selections: Dùng cho cả image polls và standard multiple choice
         * - Đếm cả "Other" option nếu được chọn
         */
        if ($poll->allow_multiple && $poll->max_image_selections) {
            $totalSelected = count($validSelectedOptions);
            if ($hasOtherSelected && $otherTextInput !== '') {
                $totalSelected++; // Count "Other" as a selection
            }
            if ($totalSelected > $poll->max_image_selections) {
                return back()->with("error", __('messages.max_selections_exceeded', ['max' => $poll->max_image_selections]));
            }
        }

        /**
         * Tạo Vote records trong DB transaction
         * - Xử lý "Other" option: Tạo PollOption mới nếu user nhập text
         * - Tạo Vote record cho mỗi selected option
         * - Gửi notification cho owner (chỉ 1 lần)
         */
        DB::transaction(function () use ($poll, $voterIdentifier, $selectedOptions, $request, $hasOtherOption, $otherTextInput) {
            // Lấy display name (giống như handleRankingVote)
            $displayName = Auth::check() ? (Auth::user()->name ?? null) : null;
            if (!$displayName) {
                $displayName = $request->session()->get("voter_name");
            }
            
            // Remove "__other__" placeholder (đã validate ở trên)
            $selectedOptions = array_filter($selectedOptions, function($optionId) {
                return $optionId !== '__other__';
            });
            
            /**
             * Xử lý "Other" option:
             * - Nếu user nhập text, tạo PollOption mới với text đó
             * - Single choice: Override selection với option mới
             * - Multiple choice: Thêm option mới vào selection
             */
            if ($hasOtherOption && $otherTextInput !== '') {
                $newOption = \App\Models\PollOption::create([
                    'poll_id' => $poll->id,
                    'option_text' => $otherTextInput,
                ]);
                // For single-choice poll, override selection with new option
                if (!$poll->allow_multiple) {
                    $selectedOptions = [$newOption->id];
                } else {
                    $selectedOptions[] = $newOption->id;
                }
            }
            
            /**
             * Tạo Vote record cho mỗi selected option
             * firstVote dùng để gửi notification (chỉ gửi 1 lần, không phải mỗi option)
             */
            $firstVote = null;
            foreach ($selectedOptions as $optionId) {
                $vote = Vote::create([
                    "poll_id" => $poll->id,
                    "poll_option_id" => $optionId,
                    "user_id" => Auth::id(), // null nếu guest
                    "ip_address" => $request->ip(),
                    "session_id" => $request->session()->getId(),
                    "voter_identifier" => $voterIdentifier,
                    "voter_name" => $displayName,
                ]);
                if ($firstVote === null) {
                    $firstVote = $vote;
                }
            }
            
            /**
             * Gửi email notification cho poll owner nếu enabled
             * (giống như handleRankingVote)
             */
            if ($firstVote && $poll->user && $poll->user->email_on_vote) {
                if (app()->environment('local')) {
                    $poll->user->notifyNow(new NewVoteNotification($poll->fresh(), $firstVote));
                } else {
                    $poll->user->notify(new NewVoteNotification($poll->fresh(), $firstVote));
                }
            }
        });

        $request->session()->put("poll_voted_" . $poll->id, true);
        return redirect()->route("polls.vote", $poll->slug)->with("success", __("messages.thank_you"));
    }
}
