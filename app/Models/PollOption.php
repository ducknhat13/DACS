<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PollOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'option_text',
        'image_url',
        'image_alt_text',
        'image_title',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'poll_option_id');
    }

    /**
     * Check if this option has an image
     */
    public function hasImage(): bool
    {
        return !empty($this->image_url);
    }

    /**
     * Get the display text for this option
     */
    public function getDisplayText(): string
    {
        if ($this->hasImage() && !empty($this->image_title)) {
            return $this->image_title;
        }
        
        return $this->option_text;
    }

    /**
     * Get the alt text for the image
     */
    public function getImageAltText(): string
    {
        return $this->image_alt_text ?: $this->getDisplayText();
    }
}
