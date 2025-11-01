<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client as GuzzleClient;

/**
 * GoogleAuthController - Controller xử lý Google OAuth authentication
 * 
 * Controller này xử lý:
 * - Redirect user đến Google OAuth login
 * - Handle callback từ Google sau khi login
 * - Link Google account với existing account
 * - Unlink Google account
 * 
 * Flow:
 * 1. redirect(): Redirect user đến Google OAuth
 * 2. callback(): Handle response từ Google
 *    - Nếu đã login: Link Google account
 *    - Nếu chưa login: Login hoặc register
 * 3. unlink(): Xóa Google OAuth link
 * 
 * Đặc biệt:
 * - Auto-verify email nếu email từ Google khớp với account
 * - Tạo random password cho OAuth-only users
 * - Prevent duplicate google_id links
 * - SSL verification có thể disable (cho local development)
 * 
 * @author QuickPoll Team
 */
class GoogleAuthController extends Controller
{
    /**
     * Lấy Google Socialite driver với cấu hình SSL
     * 
     * Nếu disable_ssl_verify = true (local development):
     * - Disable SSL verification để tránh certificate errors
     * - Chỉ dùng trong local environment
     * 
     * @return \Laravel\Socialite\Two\GoogleProvider
     */
    protected function googleDriver()
    {
        $driver = Socialite::driver('google');
        // Disable SSL verification nếu config set (cho local development)
        if (config('services.google.disable_ssl_verify')) {
            $driver->setHttpClient(new GuzzleClient(['verify' => false]));
        }
        return $driver;
    }

    /**
     * Redirect user đến Google OAuth login page
     * 
     * @return RedirectResponse
     */
    public function redirect(): RedirectResponse
    {
        return $this->googleDriver()->redirect();
    }

    /**
     * Handle callback từ Google OAuth
     * 
     * Có 2 scenarios:
     * 
     * 1. User đã login: Link Google account với existing account
     *    - Kiểm tra google_id không bị duplicate
     *    - Link google_id và auto-verify email nếu khớp
     * 
     * 2. User chưa login: Login hoặc Register
     *    - Tìm user bằng google_id hoặc email
     *    - Nếu tìm thấy: Login và attach google_id nếu chưa có
     *    - Nếu không tìm thấy: Tạo user mới với random password
     * 
     * Auto-verification:
     * - Email từ Google được auto-verify nếu khớp với account
     * - OAuth-only users không có local password (has_local_password = false)
     * 
     * @return RedirectResponse
     */
    public function callback(): RedirectResponse
    {
        $googleUser = $this->googleDriver()->user();

        /**
         * Scenario 1: User đã login → Link Google account
         */
        if (Auth::check()) {
            $current = Auth::user();

            /**
             * Kiểm tra google_id không bị duplicate
             * Prevent linking nếu google_id đã được dùng bởi user khác
             */
            $exists = User::where('google_id', $googleUser->getId())
                ->where('id', '!=', $current->id)
                ->exists();
            if ($exists) {
                return redirect()->route('profile.edit')
                    ->with('status', 'google-link-failed');
            }

            // Link Google account
            $current->google_id = $googleUser->getId();
            // Auto-verify email nếu email từ Google khớp với account
            if ($googleUser->getEmail() && $current->email === $googleUser->getEmail() && is_null($current->email_verified_at)) {
                $current->email_verified_at = now();
            }
            $current->save();

            return redirect()->route('profile.edit')->with('status', 'google-linked');
        }

        /**
         * Scenario 2: User chưa login → Login hoặc Register
         */
        // Tìm user bằng google_id hoặc email
        $user = User::where('google_id', $googleUser->getId())->first();
        if (!$user && $googleUser->getEmail()) {
            $user = User::where('email', $googleUser->getEmail())->first();
        }

        if ($user) {
            /**
             * User tồn tại: Login và attach google_id nếu chưa có
             * - Auto-verify email nếu chưa verify
             */
            if (empty($user->google_id)) {
                $user->google_id = $googleUser->getId();
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                }
                $user->save();
            }
        } else {
            /**
             * User chưa tồn tại: Tạo user mới
             * - Name: Từ Google profile hoặc fallback
             * - Email: Từ Google profile
             * - Password: Random 32 chars (OAuth-only users không cần password)
             * - has_local_password: false (chỉ dùng OAuth)
             * - Email auto-verified (từ Google)
             */
            $user = User::create([
                'name' => $googleUser->getName() ?: ($googleUser->getNickname() ?: 'User'),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(32)), // Random password (OAuth-only)
                'has_local_password' => false, // Không có local password
            ]);
            $user->google_id = $googleUser->getId();
            $user->email_verified_at = now(); // Auto-verify từ Google
            $user->save();
        }

        // Login user với "remember me" = true
        Auth::login($user, true);
        return redirect()->route('dashboard');
    }

    /**
     * Unlink Google account khỏi user account
     * 
     * Chỉ xóa google_id, không xóa account
     * User vẫn có thể đăng nhập bằng email/password (nếu có local password)
     * 
     * @return RedirectResponse
     */
    public function unlink(): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user = Auth::user();
        $user->google_id = null;
        $user->save();
        return redirect()->route('profile.edit')->with('status', 'google-unlinked');
    }
}
