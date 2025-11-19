<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use SendGrid;
use SendGrid\Mail\Mail as SendGridMail;

/**
 * SendGridMailService - Service để gửi email qua SendGrid HTTP API
 * 
 * Service này bypass SMTP (bị block trên Render) và dùng SendGrid HTTP API trực tiếp.
 * 
 * Usage:
 * ```php
 * $mailService = new SendGridMailService();
 * $mailService->send(
 *     to: 'recipient@example.com',
 *     subject: 'Subject',
 *     htmlContent: '<html>...</html>',
 *     fromAddress: 'sender@example.com',
 *     fromName: 'Sender Name',
 *     replyTo: 'reply@example.com'
 * );
 * ```
 */
class SendGridMailService
{
    private ?SendGrid $sendGrid = null;
    private ?string $apiKey = null;

    public function __construct()
    {
        $this->apiKey = config('mail.mailers.smtp.password');
        
        // Chỉ khởi tạo SendGrid nếu có API Key hợp lệ
        if ($this->apiKey && str_starts_with($this->apiKey, 'SG.')) {
            $this->sendGrid = new SendGrid($this->apiKey);
        }
    }

    /**
     * Kiểm tra xem có thể dùng SendGrid API không
     */
    public function isAvailable(): bool
    {
        return $this->sendGrid !== null;
    }

    /**
     * Gửi email qua SendGrid HTTP API
     * 
     * @param string $to Email người nhận
     * @param string $subject Tiêu đề email
     * @param string $htmlContent Nội dung HTML của email
     * @param string|null $fromAddress Email người gửi (mặc định từ config)
     * @param string|null $fromName Tên người gửi (mặc định từ config)
     * @param string|null $replyTo Email reply-to (optional)
     * @param string|null $replyToName Tên reply-to (optional)
     * @return bool True nếu gửi thành công, false nếu có lỗi
     * @throws \Exception Nếu có lỗi nghiêm trọng
     */
    public function send(
        string $to,
        string $subject,
        string $htmlContent,
        ?string $fromAddress = null,
        ?string $fromName = null,
        ?string $replyTo = null,
        ?string $replyToName = null
    ): bool {
        if (!$this->isAvailable()) {
            Log::warning('SendGrid API not available, falling back to Laravel Mail');
            return false;
        }

        try {
            $email = new SendGridMail();
            
            // From address và name
            $fromAddress = $fromAddress ?? config('mail.from.address');
            $fromName = $fromName ?? config('mail.from.name', 'DACS Poll System');
            $email->setFrom($fromAddress, $fromName);
            
            // Subject
            $email->setSubject($subject);
            
            // To
            $email->addTo($to);
            
            // Reply-to (nếu có)
            if ($replyTo) {
                $email->setReplyTo($replyTo, $replyToName);
            }
            
            // HTML content
            $email->addContent("text/html", $htmlContent);
            
            // Gửi email
            $response = $this->sendGrid->send($email);
            
            $statusCode = $response->statusCode();
            
            if ($statusCode >= 200 && $statusCode < 300) {
                Log::info("SendGrid API: Email sent successfully to {$to}");
                return true;
            } else {
                $errorBody = $response->body();
                Log::error("SendGrid API Error (Status {$statusCode}): {$errorBody}");
                throw new \Exception("SendGrid API returned status: {$statusCode}");
            }
        } catch (\Exception $e) {
            Log::error('SendGrid API Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Gửi email từ Laravel MailMessage
     * 
     * Helper method để convert Laravel MailMessage sang SendGrid format
     */
    public function sendFromMailMessage(
        \Illuminate\Notifications\Messages\MailMessage $mailMessage,
        string $to,
        ?string $fromAddress = null,
        ?string $fromName = null
    ): bool {
        // Render MailMessage thành HTML
        $htmlContent = $mailMessage->render();
        
        return $this->send(
            to: $to,
            subject: $mailMessage->subject ?? 'Notification',
            htmlContent: $htmlContent,
            fromAddress: $fromAddress,
            fromName: $fromName,
            replyTo: $mailMessage->replyTo[0] ?? null,
            replyToName: $mailMessage->replyToName ?? null
        );
    }
}

