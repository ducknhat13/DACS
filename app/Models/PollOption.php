<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * PollOption Model - Model đại diện cho một Option trong Poll
 * 
 * Một Poll có nhiều Options (lựa chọn):
 * - Standard/Ranking polls: Chỉ có option_text (text only)
 * - Image polls: Có option_text, image_url, image_title, image_alt_text
 * 
 * Đặc biệt:
 * - "Other" option: User có thể nhập text tự do (tạo PollOption mới khi vote)
 * 
 * Relationships:
 * - belongsTo Poll: Option này thuộc poll nào
 * - hasMany Vote: Các votes đã được cast cho option này
 * 
 * Helper Methods:
 * - hasImage(): Check xem option có image không
 * - getDisplayText(): Lấy text để hiển thị (image_title hoặc option_text)
 * - getImageAltText(): Lấy alt text cho image (accessibility)
 * 
 * @author QuickPoll Team
 */
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
     * Kiểm tra option có image không
     * 
     * @return bool - true nếu có image_url
     */
    public function hasImage(): bool
    {
        return !empty($this->image_url);
    }

    /**
     * Lấy text để hiển thị cho option này
     * 
     * Priority:
     * 1. image_title (nếu có image và có title)
     * 2. option_text (fallback)
     * 
     * Dùng cho:
     * - Hiển thị trong vote page
     * - Hiển thị trong results
     * - Export CSV
     * 
     * @return string - Text để hiển thị
     */
    public function getDisplayText(): string
    {
        // Ưu tiên image_title nếu có (cho image polls)
        if ($this->hasImage() && !empty($this->image_title)) {
            return $this->image_title;
        }
        
        // Fallback về option_text (cho standard polls)
        return $this->option_text;
    }

    /**
     * Lấy alt text cho image (accessibility)
     * 
     * Dùng cho HTML img tag: <img alt="...">
     * 
     * Priority:
     * 1. image_alt_text (nếu được set)
     * 2. getDisplayText() (fallback)
     * 
     * @return string - Alt text cho image
     */
    public function getImageAltText(): string
    {
        return $this->image_alt_text ?: $this->getDisplayText();
    }
}
