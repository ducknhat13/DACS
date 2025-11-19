<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * ProfileController - Controller xử lý user profile management
 * 
 * Controller này xử lý:
 * - Hiển thị và cập nhật thông tin profile (name, email)
 * - Cập nhật ngôn ngữ (locale) preference
 * - Cập nhật notification preferences (email_on_vote, notify_before_autoclose)
 * - Xóa user account (có yêu cầu password nếu có local password)
 * 
 * Đặc biệt:
 * - Email verification: Reset email_verified_at nếu email thay đổi
 * - Google OAuth users: Có thể không có local password
 * - Locale sync: Đồng bộ locale với cả database và session
 * 
 * @author QuickPoll Team
 */
class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Cập nhật ngôn ngữ (locale) của user
     * 
     * Locale được lưu ở:
     * - Database (user.locale) - Persistent
     * - Session - Để áp dụng ngay lập tức
     * 
     * Sau khi cập nhật, refresh user instance để áp dụng locale mới
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateLocale(Request $request): RedirectResponse
    {
        $request->validate([
            'lang' => ['required', 'in:vi,en'] // Chỉ cho phép 'vi' hoặc 'en'
        ]);
        $user = $request->user();
        $user->locale = $request->lang;
        $user->save();
        // Refresh user instance để áp dụng locale mới
        auth()->setUser($user->fresh());
        // Đồng bộ session để áp dụng ngay lập tức (không cần reload page)
        session(['locale' => $request->lang]);
        return redirect()->route('profile.edit')->with('success', __('messages.language_updated'));
    }

    /**
     * Cập nhật notification preferences của user
     * 
     * Notification settings:
     * - email_on_vote: Nhận email khi poll của mình có vote mới
     * - notify_before_autoclose: Nhận email nhắc nhở trước khi poll auto-close
     * 
     * Mặc định: false nếu không được gửi trong request
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateNotifications(Request $request): RedirectResponse
    {
        $request->validate([
            'email_on_vote' => ['nullable', 'boolean'],
            'notify_before_autoclose' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        // Dùng boolean() helper để đảm bảo giá trị là boolean (false nếu không có)
        $user->email_on_vote = $request->boolean('email_on_vote', false);
        $user->notify_before_autoclose = $request->boolean('notify_before_autoclose', false);
        $user->save();

        return redirect()->route('profile.edit')->with('success', __('messages.notification_settings_updated'));
    }

    /**
     * Xóa user account
     * 
     * Flow:
     * 1. Kiểm tra user có local password không
     *    - Nếu có: Yêu cầu nhập password để xác nhận
     *    - Nếu chỉ dùng Google OAuth: Không cần password
     * 2. Logout user
     * 3. Xóa user khỏi database
     * 4. Invalidate và regenerate session
     * 5. Redirect về home
     * 
     * Security:
     * - Yêu cầu password nếu user có local password
     * - current_password validation: Kiểm tra password đúng
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        /**
         * Kiểm tra user có local password không
         * - has_local_password = true: User đã tạo password local
         * - Hoặc user có password trong database
         * Nếu có → yêu cầu password để xác nhận
         */
        $requiresPassword = (bool) ($user->has_local_password ?? !empty($user->password));

        if ($requiresPassword) {
            // Yêu cầu password để xác nhận xóa account
            $request->validateWithBag('userDeletion', [
                'password' => ['required', 'current_password'], // current_password: Laravel validation rule
            ]);
        }

        // Logout trước khi xóa để đảm bảo session được clear
        Auth::logout();

        // Xóa user (cascade delete sẽ xóa polls, votes, comments liên quan nếu có)
        $user->delete();

        // Invalidate session và regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
