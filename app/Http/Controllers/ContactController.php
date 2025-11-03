<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactMail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

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
            
            // Log mail config để debug (không log password) - output trực tiếp ra stderr
            $mailConfig = [
                'to' => $supportEmail,
                'from' => config('mail.from.address'),
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'username_set' => !empty(config('mail.mailers.smtp.username')),
                'password_set' => !empty(config('mail.mailers.smtp.password')),
                'queue_connection' => config('queue.default'),
            ];
            
            // Log cả vào file và stderr
            Log::info('=== Contact Form: Attempting to send email ===', $mailConfig);
            error_log('=== Contact Form: Attempting to send email ===');
            error_log('Mail Config: ' . json_encode($mailConfig, JSON_PRETTY_PRINT));
            
            /**
             * Gửi email đến support team
             * ContactMail sẽ hiển thị thông tin người gửi và message
             * Reply-to được set thành email người gửi để có thể reply trực tiếp
             */
            error_log('Before Mail::send() call');
            
            // LƯU Ý QUAN TRỌNG:
            // - Nếu QUEUE_CONNECTION=database: Mail sẽ được đẩy vào queue, nhưng CẦN queue worker để process
            // - Render free tier KHÔNG hỗ trợ background worker, nên mail sẽ KHÔNG được gửi
            // - Giải pháp: Dùng QUEUE_CONNECTION=sync để gửi mail trực tiếp
            // - Hoặc: Upgrade Render plan để chạy queue worker
            
            $queueConnection = config('queue.default');
            error_log("Queue connection: $queueConnection");
            
            if ($queueConnection === 'database') {
                error_log("WARNING: Queue connection is 'database' but no queue worker is running!");
                error_log("Mail will be queued but NOT sent without a worker.");
                error_log("Solution: Set QUEUE_CONNECTION=sync in Render environment variables");
            }
            
            // Nếu queue = database, mail sẽ được queue thay vì gửi ngay
            // Nhưng cần queue worker để process, nếu không mail sẽ không được gửi
            Mail::to($supportEmail)->send(new ContactMail(
                $request->name,
                $request->email,
                $request->subject,
                $request->message
            ));
            
            error_log('After Mail::send() call - Success');
            Log::info('Contact email sent successfully');

            return back()->with('success', __('messages.contact_success'));
        } catch (TransportExceptionInterface $e) {
            /**
             * Catch SMTP/Transport exceptions cụ thể
             * Đây là exception từ SwiftMailer/Symfony Mailer khi không thể kết nối SMTP
             */
            $errorMessage = $e->getMessage();
            $isTimeout = stripos($errorMessage, 'timeout') !== false || stripos($errorMessage, 'connection timed out') !== false;
            
            $errorDetails = [
                'exception' => get_class($e),
                'message' => $errorMessage,
                'code' => $e->getCode(),
                'is_timeout' => $isTimeout,
                'mail_config' => [
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                    'username' => config('mail.mailers.smtp.username'),
                ],
                'possible_causes' => $isTimeout ? [
                    'Render firewall may block outbound SMTP connections',
                    'Gmail may block Render IP addresses',
                    'Try using port 465 with SSL instead of 587 with TLS',
                    'Consider using alternative mail service (SendGrid, Mailgun)',
                ] : [],
            ];
            
            // Log ra cả file và stderr
            Log::error('Contact form: SMTP Transport Exception', $errorDetails);
            error_log('=== Contact Form: SMTP Transport Exception ===');
            error_log('Error: ' . $errorMessage);
            error_log('Is Timeout: ' . ($isTimeout ? 'YES' : 'NO'));
            error_log('Details: ' . json_encode($errorDetails, JSON_PRETTY_PRINT));
            
            // Hiển thị error message chi tiết nếu debug mode
            if (config('app.debug')) {
                $errorMessage = __('messages.contact_error') . ' (SMTP: ' . $errorMessage . ')';
            } else {
                // Trong production, hiển thị message thân thiện hơn
                if ($isTimeout) {
                    $errorMessage = __('messages.contact_error') . ' (SMTP connection timeout. This may be due to network restrictions. Please try again later or contact support.)';
                } else {
                    $errorMessage = __('messages.contact_error');
                }
            }
            
            return back()
                ->withInput()
                ->with('error', $errorMessage);
        } catch (\Exception $e) {
            /**
             * Error handling cho các exception khác:
             * - Log error message và stack trace để debug
             * - Trong local env: Hiển thị error message chi tiết (có exception message)
             * - Trong production: Log chi tiết, nhưng chỉ hiển thị generic error message
             * - Giữ lại input data để user không phải nhập lại
             */
            $errorDetails = [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'mail_config' => [
                    'mailer' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'from' => config('mail.from.address'),
                ],
            ];
            
            // Log ra cả file và stderr
            Log::error('Contact form: General Exception', $errorDetails);
            error_log('=== Contact Form: General Exception ===');
            error_log('Error: ' . $e->getMessage());
            error_log('Details: ' . json_encode($errorDetails, JSON_PRETTY_PRINT));
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            // Show detailed error in development để dễ debug
            $errorMessage = (app()->environment('local') || config('app.debug'))
                ? __('messages.contact_error') . ' (' . $e->getMessage() . ')'
                : __('messages.contact_error');
            
            return back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }
}

