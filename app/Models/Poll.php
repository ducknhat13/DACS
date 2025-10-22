<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
     * Get the maximum number of selections allowed for image polls
     */
    public function getMaxSelections(): ?int
    {
        if ($this->isImagePoll()) {
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
}