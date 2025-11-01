<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Poll Model - Model đại diện cho một Poll (Khảo sát)
 * 
 * Các loại Poll:
 * - 'standard': Poll thông thường (single/multiple choice)
 * - 'ranking': Poll xếp hạng (user phải xếp hạng tất cả options)
 * - 'image': Poll với hình ảnh (luôn multiple choice)
 * 
 * Quan hệ:
 * - belongsTo User: Poll được tạo bởi user nào
 * - hasMany PollOption: Các lựa chọn của poll
 * - hasMany Vote: Các votes đã được cast
 * - hasMany Comment: Các bình luận (nếu enabled)
 * 
 * Đặc biệt:
 * - max_image_selections: Reused cho cả image polls và standard multiple choice
 * - voter_identifier: Dùng để track unique participants (prevent duplicate votes)
 * 
 * @author QuickPoll Team
 */
class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'description_media',
        'question',
        'slug',
        'allow_multiple',
        'is_closed',
        'is_private',
        'access_key',
        'poll_type',
        'max_image_selections',
        'voting_security',
        'auto_close_at',
        'allow_comments',
        'hide_share',
    ];

    protected $casts = [
        'allow_multiple' => 'boolean',
        'is_closed' => 'boolean',
        'is_private' => 'boolean',
        'allow_comments' => 'boolean',
        'hide_share' => 'boolean',
        'auto_close_at' => 'datetime',
        'max_image_selections' => 'integer',
        'description_media' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Check if this poll is an image poll
     */
    public function isImagePoll(): bool
    {
        return $this->poll_type === 'image';
    }

    /**
     * Get the maximum number of selections allowed
     * For image polls: returns max_image_selections
     * For standard polls with multiple choice: returns max_image_selections (stored from max_choices)
     * For standard polls with single choice: returns 1
     */
    public function getMaxSelections(): ?int
    {
        if ($this->isImagePoll()) {
            return $this->max_image_selections;
        }
        
        if ($this->poll_type === 'standard' && $this->allow_multiple) {
            // For standard multiple choice polls, max_image_selections contains max_choices value
            return $this->max_image_selections;
        }
        
        return $this->allow_multiple ? null : 1;
    }

    /**
     * Get description media files
     */
    public function getDescriptionMedia(): array
    {
        return $this->description_media ?? [];
    }

    /**
     * Check if poll has description media
     */
    public function hasDescriptionMedia(): bool
    {
        return !empty($this->description_media);
    }

    /**
     * Tính số lượng unique participants (voters)
     * 
     * Đếm số người tham gia unique thay vì tổng số votes vì:
     * - Ranking polls: 1 user vote = nhiều Vote records (1 cho mỗi option)
     * - Multiple choice polls: 1 user có thể vote nhiều options
     * 
     * Sử dụng distinct('voter_identifier') để:
     * - Logged in users: "user_{id}" (persistent)
     * - Guests: "session_{session_id}" (reset khi clear cookies)
     * 
     * @return int - Số lượng unique participants
     */
    public function getParticipantsCountAttribute(): int
    {
        // Cả ranking và regular polls đều dùng distinct voter_identifier
        // để đảm bảo đếm chính xác (1 user = 1 participant)
        return $this->votes()->distinct('voter_identifier')->count('voter_identifier');
    }
}