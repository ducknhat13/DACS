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
        'question',
        'slug',
        'allow_multiple',
        'is_closed',
        'is_private',
        'access_key',
        'poll_type',
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
}