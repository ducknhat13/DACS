<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\Vote;
// use App\Events\NewVoteCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $poll = Poll::with("options")->where("slug", $slug)->firstOrFail();
        
        if ($poll->is_closed) {
            return back()->with("error", __("messages.poll_is_closed"));
        }

        $sessionId = $request->session()->getId();
        $voterIdentifier = Auth::check() ? "user_" . Auth::id() : "session_" . $sessionId;
        
        // Check if user has already voted
        $isOwner = Auth::check() && Auth::id() === $poll->user_id;
        $existingVote = Vote::where("poll_id", $poll->id)
            ->where("voter_identifier", $voterIdentifier)
            ->exists();
            
        // Prevent re-voting for everyone (owner and guests)
        if ($existingVote) {
            return back()->with("error", __("messages.you_have_voted"));
        }

        if ($poll->poll_type === "ranking") {
            return $this->handleRankingVote($request, $poll, $voterIdentifier);
        } else {
            return $this->handleRegularVote($request, $poll, $voterIdentifier);
        }
    }

    private function handleRankingVote(Request $request, Poll $poll, string $voterIdentifier)
    {
        $ranking = $request->input("ranking", []);
        // Accept JSON string from hidden input and normalize to array
        if (is_string($ranking)) {
            $decoded = json_decode($ranking, true);
            $ranking = is_array($decoded) ? $decoded : [];
        }
        
        if (empty($ranking) || !is_array($ranking)) {
            return back()->with("error", __("messages.pls_rank"));
        }

        $optionIds = $poll->options->pluck("id")->toArray();
        $rankingOptionIds = array_keys($ranking);
        
        // Check if all options have been ranked
        if (count($rankingOptionIds) !== count($optionIds)) {
            return back()->with("error", __("messages.pls_rank"));
        }
        
        // Check if any option is missing from ranking
        foreach ($optionIds as $optionId) {
            if (!isset($ranking[$optionId])) {
                return back()->with("error", __("messages.pls_rank"));
            }
        }

        DB::transaction(function () use ($poll, $voterIdentifier, $ranking, $request) {
            $displayName = Auth::check() ? (Auth::user()->name ?? null) : null;
            if (!$displayName) {
                $displayName = $request->session()->get("voter_name");
            }
            foreach ($ranking as $optionId => $rank) {
                $vote = Vote::create([
                    "poll_id" => $poll->id,
                    "poll_option_id" => $optionId,
                    "user_id" => Auth::id(),
                    "ip_address" => $request->ip(),
                    "session_id" => $request->session()->getId(),
                    "voter_identifier" => $voterIdentifier,
                    "voter_name" => $displayName,
                    "rank" => (int) $rank,
                ]);
                // event(new NewVoteCreated($vote));
            }
        });

        $request->session()->put("poll_voted_" . $poll->id, true);
        return redirect()->route("polls.vote", $poll->slug)->with("success", __("messages.thank_you"));
    }

    private function handleRegularVote(Request $request, Poll $poll, string $voterIdentifier)
    {
        $selectedOptions = $request->input("options", []);
        $otherTextInput = trim((string)$request->input('other_option'));
        $hasOtherOption = $poll->options->contains(function($opt){
            return strtolower(trim($opt->option_text)) === 'other';
        });
        
        // Ensure selectedOptions is always an array
        if (!is_array($selectedOptions)) {
            $selectedOptions = [$selectedOptions];
        }
        
        // Remove "__other__" placeholder from selectedOptions for validation
        $validSelectedOptions = array_filter($selectedOptions, function($optionId) {
            return $optionId !== '__other__';
        });
        
        // Check if user selected "Other" but didn't provide text
        $hasOtherSelected = in_array('__other__', $selectedOptions);
        if ($hasOtherSelected && $otherTextInput === '') {
            return back()->with("error", __("messages.pls_enter_other_text"));
        }
        
        // Check if no valid options are selected
        if (empty($validSelectedOptions) && !($hasOtherOption && $otherTextInput !== '')) {
            return back()->with("error", __("messages.pls_select"));
        }

        if (!$poll->allow_multiple && count($validSelectedOptions) > 1) {
            return back()->with("error", __("messages.only_one"));
        }

        DB::transaction(function () use ($poll, $voterIdentifier, $selectedOptions, $request, $hasOtherOption, $otherTextInput) {
            $displayName = Auth::check() ? (Auth::user()->name ?? null) : null;
            if (!$displayName) {
                $displayName = $request->session()->get("voter_name");
            }
            
            // Remove "__other__" placeholder from selectedOptions
            $selectedOptions = array_filter($selectedOptions, function($optionId) {
                return $optionId !== '__other__';
            });
            
            // If guest typed "Other" option, create it before saving votes
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
            
            foreach ($selectedOptions as $optionId) {
                $vote = Vote::create([
                    "poll_id" => $poll->id,
                    "poll_option_id" => $optionId,
                    "user_id" => Auth::id(),
                    "ip_address" => $request->ip(),
                    "session_id" => $request->session()->getId(),
                    "voter_identifier" => $voterIdentifier,
                    "voter_name" => $displayName,
                ]);
                // event(new NewVoteCreated($vote));
            }
        });

        $request->session()->put("poll_voted_" . $poll->id, true);
        return redirect()->route("polls.vote", $poll->slug)->with("success", __("messages.thank_you"));
    }
}
