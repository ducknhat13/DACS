<?php

namespace App\Notifications;

use App\Models\Poll;
use App\Models\Vote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * NewVoteNotification - Email notification khi có vote mới trên poll
 * 
 * Notification này được gửi đến poll owner khi:
 * - Có người vote trên poll của họ
 * - User đã bật notification preference: email_on_vote = true
 * 
 * Implementation:
 * - Implements ShouldQueue: Email được queue để xử lý async (trừ local env)
 * - Trong local: notifyNow() để gửi sync (test với Mailpit)
 * - Trong production: notify() để queue (async)
 * 
 * Email content:
 * - Subject: "New vote on your poll: {poll title}"
 * - Body: Poll title, voter name, link đến poll results
 * 
 * @author QuickPoll Team
 */
class NewVoteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Poll $poll;
    public Vote $vote;

    /**
     * Create a new notification instance.
     * 
     * @param Poll $poll - Poll có vote mới
     * @param Vote $vote - Vote đầu tiên (dùng để lấy voter_name)
     */
    public function __construct(Poll $poll, Vote $vote)
    {
        $this->poll = $poll;
        $this->vote = $vote;
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
     * - Body: "Someone just voted on your poll '{poll}'. Voter: {voter_name}"
     * - Action button: Link đến poll results page
     * - Footer: "Thank you for using QuickPoll!"
     * 
     * @param object $notifiable - User nhận notification (poll owner)
     * @return MailMessage - Laravel MailMessage instance
     */
    public function toMail(object $notifiable): MailMessage
    {
        // URL đến trang kết quả poll
        $pollUrl = route('polls.show', $this->poll->slug);
        
        return (new MailMessage)
            ->subject(__('messages.new_vote_notification_subject', ['poll' => $this->poll->title ?? $this->poll->question]))
            ->greeting(__('messages.hello', ['name' => $notifiable->name]))
            ->line(__('messages.new_vote_notification_body', [
                'poll' => $this->poll->title ?? $this->poll->question,
                'voter' => $this->vote->voter_name ?? __('messages.anonymous'), // Hiển thị "Anonymous" nếu không có tên
            ]))
            ->action(__('messages.view_poll'), $pollUrl) // Button link đến poll results
            ->line(__('messages.thank_you_for_using'));
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
            'vote_id' => $this->vote->id,
        ];
    }
}
