<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\ContactMail;

/**
 * ContactController - Controller xử lý contact form
 * 
 * Controller này xử lý việc gửi email từ contact form:
 * - Validate form data (name, email, subject, message)
 * - Gửi email đến support email (config trong config/mail.php)
 * - Error handling và logging cho debugging
 * 
 * Email được gửi qua ContactMail mailable class và hiển thị
 * thông tin người gửi, subject, và message.
 * 
 * @author QuickPoll Team
 */
class ContactController extends Controller
{
    /**
     * Xử lý submit contact form
     * 
     * Flow:
     * 1. Validate form data (name, email, subject, message)
     * 2. Lấy support email từ config (hoặc .env)
     * 3. Gửi email qua ContactMail mailable
     * 4. Return success message hoặc error với details
     * 
     * Error handling:
     * - Log errors để debug
     * - Hiển thị error message chi tiết trong local env
     * - Giữ lại input data khi có lỗi validation
     * 
     * @param Request $request - Form data từ contact form
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', __('messages.contact_validation_error'));
        }

        try {
            /**
             * Lấy support email từ config hoặc .env
             * Priority: config('mail.support_email') > env('SUPPORT_EMAIL') > default
             */
            $supportEmail = config('mail.support_email', env('SUPPORT_EMAIL', 'support@quickpoll.com'));
            
            // Log mail config để debug (không log password)
            \Log::info('Attempting to send contact email', [
                'to' => $supportEmail,
                'from' => config('mail.from.address'),
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'username_set' => !empty(config('mail.mailers.smtp.username')),
                'password_set' => !empty(config('mail.mailers.smtp.password')),
            ]);
            
            /**
             * Gửi email đến support team
             * ContactMail sẽ hiển thị thông tin người gửi và message
             * Reply-to được set thành email người gửi để có thể reply trực tiếp
             */
            Mail::to($supportEmail)->send(new ContactMail(
                $request->name,
                $request->email,
                $request->subject,
                $request->message
            ));
            
            \Log::info('Contact email sent successfully');

            return back()->with('success', __('messages.contact_success'));
        } catch (\Exception $e) {
            /**
             * Error handling:
             * - Log error message và stack trace để debug
             * - Trong local env: Hiển thị error message chi tiết (có exception message)
             * - Trong production: Log chi tiết, nhưng chỉ hiển thị generic error message
             * - Giữ lại input data để user không phải nhập lại
             */
            \Log::error('Contact form error: ' . $e->getMessage());
            \Log::error('Contact form stack trace: ' . $e->getTraceAsString());
            \Log::error('Mail config - MAIL_MAILER: ' . config('mail.default'));
            \Log::error('Mail config - MAIL_HOST: ' . config('mail.mailers.smtp.host'));
            \Log::error('Mail config - MAIL_FROM_ADDRESS: ' . config('mail.from.address'));
            \Log::error('Support Email: ' . $supportEmail);
            
            // Show detailed error in development để dễ debug
            // Trong production, vẫn log chi tiết nhưng chỉ hiển thị generic message
            $errorMessage = app()->environment('local') 
                ? __('messages.contact_error') . ' (' . $e->getMessage() . ')'
                : __('messages.contact_error');
            
            return back()
                ->withInput() // Giữ lại input data
                ->with('error', $errorMessage);
        }
    }
}

