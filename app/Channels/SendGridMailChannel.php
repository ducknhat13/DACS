<?php

namespace App\Channels;

use App\Services\SendGridMailService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * SendGridMailChannel - Custom notification channel dùng SendGrid HTTP API
 * 
 * Channel này bypass SMTP (bị block trên Render) và dùng SendGrid HTTP API trực tiếp.
 * 
 * Usage trong Notification:
 * ```php
 * public function via($notifiable)
 * {
 *     $sendGridService = new SendGridMailService();
 *     if ($sendGridService->isAvailable()) {
 *         return [SendGridMailChannel::class];
 *     }
 *     return ['mail']; // Fallback to Laravel Mail
 * }
 * ```
 */
class SendGridMailChannel
{
    public function send($notifiable, Notification $notification)
    {
        $sendGridService = new SendGridMailService();
        
        if (!$sendGridService->isAvailable()) {
            Log::warning('SendGridMailChannel: SendGrid API not available, notification will be skipped');
            return;
        }
        
        if (!method_exists($notification, 'toMail')) {
            return;
        }
        
        $mailMessage = $notification->toMail($notifiable);
        
        if (!$mailMessage) {
            return;
        }
        
        try {
            // Render MailMessage thành HTML
            $greeting = $mailMessage->greeting ?? null;
            $introLines = $mailMessage->introLines ?? [];
            $outroLines = $mailMessage->outroLines ?? [];
            $actionText = $mailMessage->actionText ?? null;
            $actionUrl = $mailMessage->actionUrl ?? null;
            
            $htmlContent = view('emails.simple', [
                'greeting' => $greeting,
                'introLines' => $introLines,
                'actionText' => $actionText,
                'actionUrl' => $actionUrl,
                'outroLines' => $outroLines,
            ])->render();
            
            $sent = $sendGridService->send(
                to: $notifiable->email ?? $notifiable->routeNotificationFor('mail'),
                subject: $mailMessage->subject,
                htmlContent: $htmlContent
            );
            
            if ($sent) {
                Log::info('Notification sent successfully via SendGrid API');
            }
        } catch (\Exception $e) {
            Log::error('SendGridMailChannel failed: ' . $e->getMessage());
            error_log('SendGridMailChannel Exception: ' . $e->getMessage());
        }
    }
}

