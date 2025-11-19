<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Vote Model - Model đại diện cho một Vote (Bình chọn)
 * 
 * Một Vote record đại diện cho 1 lựa chọn của user:
 * - Standard polls: 1 vote = 1 option được chọn
 * - Multiple choice polls: 1 user có thể tạo nhiều Vote records
 * - Ranking polls: 1 user tạo nhiều Vote records (1 cho mỗi option với rank)
 * 
 * Tracking:
 * - voter_identifier: "user_{id}" hoặc "session_{session_id}" để track unique participants
 * - voter_name: Tên người vote (từ account hoặc session)
 * - ip_address, session_id: Để tracking và analytics
 * 
 * Relationships:
 * - belongsTo Poll: Vote này thuộc poll nào
 * - belongsTo PollOption: Vote này cho option nào
 * - belongsTo User: Vote này từ user nào (null nếu guest)
 * 
 * @author QuickPoll Team
 */
class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_option_id',
        'poll_id', 
        'user_id',
        'ip_address',
        'session_id',
        'voter_identifier',
        'voter_name',
        'rank',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(PollOption::class, 'poll_option_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}