<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model - Model đại diện cho User (Người dùng)
 * 
 * User có thể:
 * - Tạo Poll (polls relationship)
 * - Vote trên Polls (votes relationship)
 * - Đăng nhập qua email/password hoặc Google OAuth
 * 
 * Notification Preferences:
 * - email_on_vote: Nhận email khi poll của mình có vote mới
 * - notify_before_autoclose: Nhận email nhắc nhở trước khi poll auto-close
 * 
 * Authentication:
 * - google_id: ID từ Google OAuth (null nếu không dùng Google login)
 * - has_local_password: Boolean, true nếu có password local (không chỉ OAuth)
 * - locale: Ngôn ngữ preference (vi|en)
 * 
 * @author QuickPoll Team
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'locale',
        'has_local_password',
        'email_on_vote',
        'notify_before_autoclose',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'has_local_password' => 'boolean',
            'email_on_vote' => 'boolean',
            'notify_before_autoclose' => 'boolean',
        ];
    }
}
