<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

/**
 * Comment Model - Model đại diện cho một Comment (Bình luận) trên Poll
 * 
 * Comments được enable/disable bởi poll.allow_comments flag
 * 
 * User tracking:
 * - user_id: Nếu logged in user comment (null nếu guest)
 * - voter_name: Tên người comment (từ account hoặc session)
 * - session_id: Session ID để track guest comments
 * - ip_address: IP address để tracking và analytics
 * 
 * Relationships:
 * - belongsTo Poll: Comment này thuộc poll nào
 * - belongsTo User: Comment này từ user nào (null nếu guest)
 * 
 * @author QuickPoll Team
 */
class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id', 'user_id', 'voter_name', 'content', 'session_id', 'ip_address',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


