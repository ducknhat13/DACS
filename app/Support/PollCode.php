<?php

namespace App\Support;

use App\Models\Poll;

/**
 * PollCode - Helper class để generate unique poll slugs
 * 
 * Class này tạo ra các slug thân thiện với người dùng cho polls
 * Format: noun-verb-adjective (danh-từ-động-từ-tính-từ)
 * 
 * Ví dụ: "meo-chay-xanh", "ca-hat-do", "bien-doc-sang"
 * 
 * Đặc điểm:
 * - Dùng từ tiếng Việt không dấu (dễ đọc, dễ nhớ)
 * - Format: 3 từ nối bằng dấu gạch ngang
 * - Đảm bảo unique: Kiểm tra database trước khi return
 * - Fallback: Nếu collision, thêm random string vào cuối
 * 
 * @author QuickPoll Team
 */
class PollCode
{
    /**
     * Generate một unique slug cho poll
     * 
     * Flow:
     * 1. Generate slug ngẫu nhiên (noun-verb-adjective)
     * 2. Kiểm tra xem đã tồn tại trong database chưa
     * 3. Nếu chưa → return
     * 4. Nếu đã tồn tại → generate lại (tối đa $maxAttempts lần)
     * 5. Nếu vẫn collision → thêm random string vào cuối
     * 
     * @param int $maxAttempts - Số lần thử tối đa trước khi dùng fallback
     * @return string - Unique slug (ví dụ: "meo-chay-xanh" hoặc "meo-chay-xanh-a3f2")
     */
    public static function generateUniqueSlug(int $maxAttempts = 10): string
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            $slug = self::generate();
            if (!Poll::where('slug', $slug)->exists()) {
                return $slug;
            }
        }

        // Fallback to a more random variant if collisions persist
        return self::generate() . '-' . substr(strtolower(bin2hex(random_bytes(2))), 0, 4);
    }

    /**
     * Generate một slug ngẫu nhiên (không check unique)
     * 
     * Format: noun-verb-adjective (danh-từ-động-từ-tính-từ)
     * 
     * Ví dụ: "meo-chay-xanh", "ca-hat-do", "bien-doc-sang"
     * 
     * @return string - Slug format (ví dụ: "meo-chay-xanh")
     */
    public static function generate(): string
    {
        /**
         * Danh sách từ tiếng Việt không dấu, đơn giản, dễ đọc
         * - Nouns: Danh từ (con vật, đồ vật, thiên nhiên)
         * - Verbs: Động từ (hành động)
         * - Adjectives: Tính từ (màu sắc, tính chất)
         */
        $nouns = [
            'meo','cho','chim','ca','cay','bien','nui','sao','mua','nang',
            'trang','may','gio','lua','song','la','hoa','ban','banh','sua',
            'banhmi','sach','but','vo','banhxeo','tra','caPhe','ban','banDo','nha',
        ];
        $verbs = [
            'chay','nhay','bay','boi','noi','cuoi','hat','doc','viet','ve',
            'nghi','choi','hoc','lam','mo','moCua','xem','chiaSe','binhChon','chon',
        ];
        $adjectives = [
            'xanh','do','vang','den','trang','tim','hong','nau','cam','xam',
            'nhanh','manh','sang','sach','dep','vui','tot','gan','xa','moi',
        ];

        /**
         * Cấu trúc: danh-từ-động-từ-tính-từ
         * Chọn ngẫu nhiên 1 từ mỗi loại và nối bằng dấu gạch ngang
         */
        $w1 = $nouns[array_rand($nouns)];
        $w2 = $verbs[array_rand($verbs)];
        $w3 = $adjectives[array_rand($adjectives)];

        // Normalize và join các từ
        $parts = [self::normalize($w1), self::normalize($w2), self::normalize($w3)];
        return implode('-', $parts);
    }

    /**
     * Normalize từ (loại bỏ ký tự đặc biệt, lowercase)
     * 
     * - Chuyển về lowercase
     * - Giữ lại a-z, 0-9
     * - Thay các ký tự khác bằng '-'
     * - Loại bỏ '-' ở đầu và cuối
     * 
     * @param string $word - Từ cần normalize
     * @return string - Từ đã được normalize
     */
    public static function normalize(string $word): string
    {
        $word = strtolower($word);
        // Giữ lại a-z 0-9, thay các ký tự khác bằng '-'
        $word = preg_replace('/[^a-z0-9]+/i', '-', $word) ?? '';
        // Loại bỏ '-' ở đầu và cuối
        $word = trim($word, '-');
        return $word;
    }
}


