<?php

namespace App\Http\Middleware;

use App\Models\Poll;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePollAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('slug');
        if (!$slug) {
            return $next($request);
        }
        $poll = Poll::where('slug', $slug)->first();
        if (!$poll) {
            return $next($request);
        }
        // Auto-close enforcement
        if ($poll->auto_close_at && now()->greaterThanOrEqualTo($poll->auto_close_at)) {
            $poll->is_closed = true;
            $poll->save();
        }
        // Private via voting_security (not for owner)
        if (($poll->voting_security === 'private') || $poll->is_private) {
            // Check if user is the owner
            $isOwner = Auth::check() && Auth::id() === $poll->user_id;
            
            if (!$isOwner && !session()->get('poll_access_'.$poll->id)) {
                return redirect()->route('polls.access', $poll->slug);
            }
        }
        
        // Share poll data with request
        $request->attributes->set('poll', $poll);
        
        return $next($request);
    }
}


