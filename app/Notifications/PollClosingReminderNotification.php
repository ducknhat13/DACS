<?php

namespace App\Notifications;

use App\Models\Poll;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * PollClosingReminderNotification - Email notification nhắc nhở trước khi poll auto-close
 * 
 * Notification này được gửi đến poll owner khi:
 * - Poll có auto_close_at được set
 * - Poll sắp đóng (có thể schedule job để gửi trước 1 ngày, 1 giờ, etc.)
 * - User đã bật notification preference: notify_before_autoclose = true
 * 
 * Implementation:
 * - Implements ShouldQueue: Email được queue để xử lý async
 * - Cần schedule job để gửi notification trước khi poll đóng
 * 
 * Email content:
 * - Subject: "Reminder: Your poll '{poll}' is closing soon"
 * - Body: Poll title, closing date/time, link đến poll results
 * 
 * @author QuickPoll Team
 */
class PollClosingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Poll $poll;

    /**
     * Create a new notification instance.
     * 
     * @param Poll $poll - Poll sắp đóng
     */
    public function __construct(Poll $poll)
    {
        $this->poll = $poll;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Tạo email message cho notification
     * 
     * Email format:
     * - Greeting: "Hello, {name}!"
     * - Body: "Your poll '{poll}' will automatically close on {date}. Make sure to review the results before then."
     * - Action button: Link đến poll results page
     * - Footer: "The poll will automatically close at the scheduled time."
     * 
     * @param object $notifiable - User nhận notification (poll owner)
     * @return MailMessage - Laravel MailMessage instance
     */
    public function toMail(object $notifiable): MailMessage
    {
        // URL đến trang kết quả poll
        $pollUrl = route('polls.show', $this->poll->slug);
        // Format closing date theo định dạng người dùng (dd/mm/yyyy HH:mm)
        $closingDate = $this->poll->auto_close_at->format('d/m/Y H:i');
        
        return (new MailMessage)
            ->subject(__('messages.poll_closing_reminder_subject', ['poll' => $this->poll->title ?? $this->poll->question]))
            ->greeting(__('messages.hello', ['name' => $notifiable->name]))
            ->line(__('messages.poll_closing_reminder_body', [
                'poll' => $this->poll->title ?? $this->poll->question,
                'date' => $closingDate,
            ]))
            ->action(__('messages.view_poll'), $pollUrl) // Button link đến poll results
            ->line(__('messages.poll_will_auto_close_soon'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'poll_id' => $this->poll->id,
            'poll_title' => $this->poll->title ?? $this->poll->question,
            'auto_close_at' => $this->poll->auto_close_at,
        ];
    }
}
