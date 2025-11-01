<?php

namespace App\Http\Middleware;

use App\Models\Poll;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsurePollAccess Middleware - Kiểm tra quyền truy cập poll
 * 
 * Middleware này được áp dụng cho các routes liên quan đến poll (vote, show, etc.)
 * 
 * Chức năng:
 * - Tự động đóng poll nếu đã qua thời gian auto_close_at
 * - Kiểm tra private poll: Redirect đến access form nếu chưa có access key
 * - Owner luôn có quyền truy cập (bỏ qua access check)
 * - Share poll data với request để tránh query lại trong controller
 * 
 * Flow:
 * 1. Lấy slug từ route parameter
 * 2. Load poll từ database
 * 3. Auto-close check (nếu cần)
 * 4. Private poll check:
 *    - Nếu là owner → cho phép
 *    - Nếu đã có session 'poll_access_{id}' → cho phép
 *    - Ngược lại → redirect đến access form
 * 5. Attach poll vào request attributes
 * 
 * @author QuickPoll Team
 */
class EnsurePollAccess
{
    /**
     * Handle an incoming request.
     * 
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * Lấy slug từ route parameter
         * Nếu không có slug, tiếp tục request (không phải poll route)
         */
        $slug = $request->route('slug');
        if (!$slug) {
            return $next($request);
        }
        
        /**
         * Load poll từ database
         * Nếu không tìm thấy, tiếp tục request (404 sẽ được handle ở controller)
         */
        $poll = Poll::where('slug', $slug)->first();
        if (!$poll) {
            return $next($request);
        }
        
        /**
         * Tự động đóng poll nếu đã qua thời gian auto_close_at
         * Check ở middleware để đảm bảo poll được đóng ngay khi truy cập
         */
        if ($poll->auto_close_at && now()->greaterThanOrEqualTo($poll->auto_close_at)) {
            $poll->is_closed = true;
            $poll->save();
        }
        
        /**
         * Kiểm tra private poll:
         * - Check cả voting_security === 'private' và is_private flag
         * - Owner luôn có quyền truy cập (bỏ qua check)
         * - Nếu đã có session 'poll_access_{poll_id}' → đã nhập access key → cho phép
         * - Ngược lại → redirect đến access form
         */
        if (($poll->voting_security === 'private') || $poll->is_private) {
            // Owner luôn có quyền truy cập
            $isOwner = Auth::check() && Auth::id() === $poll->user_id;
            
            // Nếu không phải owner và chưa có access key trong session → redirect
            if (!$isOwner && !session()->get('poll_access_'.$poll->id)) {
                return redirect()->route('polls.access', $poll->slug);
            }
        }
        
        /**
         * Share poll data với request để controller không phải query lại
         * Controller có thể lấy: $poll = $request->attributes->get('poll')
         */
        $request->attributes->set('poll', $poll);
        
        return $next($request);
    }
}


