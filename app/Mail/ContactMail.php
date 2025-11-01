<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * ContactMail - Mailable class cho contact form emails
 * 
 * Email này được gửi đến support team khi user submit contact form
 * 
 * Email content (trong resources/views/emails/contact.blade.php):
 * - Name: Tên người gửi
 * - Email: Email người gửi (có thể click để reply)
 * - Subject: Tiêu đề (user tự nhập, không có prefix "Contact Form: ")
 * - Message: Nội dung message từ user
 * 
 * Đặc biệt:
 * - Reply-to được set thành email người gửi để có thể reply trực tiếp
 * - Subject không có prefix "Contact Form: " (user nhập gì gửi đấy)
 * 
 * @author QuickPoll Team
 */
class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $subject;
    public $messageText; // Renamed từ $message để tránh conflict với Laravel MailMessage

    /**
     * Create a new message instance.
     * 
     * @param string $name - Tên người gửi
     * @param string $email - Email người gửi (dùng cho reply-to)
     * @param string $subject - Tiêu đề email (user tự nhập)
     * @param string $message - Nội dung message
     */
    public function __construct($name, $email, $subject, $message)
    {
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->messageText = $message; // Lưu vào messageText để tránh conflict
    }

    /**
     * Get the message envelope (subject, from, reply-to).
     * 
     * @return Envelope - Email envelope với subject và reply-to
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject, // Dùng subject user nhập, KHÔNG thêm prefix "Contact Form: "
            replyTo: [$this->email], // Set reply-to để có thể reply trực tiếp
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
