<?php

namespace App\Support;

use App\Models\Poll;

class PollCode
{
    /**
     * Generate a unique, human-friendly slug in format word-word-word
     * using simple Vietnamese words without diacritics.
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

    public static function generate(): string
    {
        // Danh sách từ không dấu, đơn giản, dễ đọc
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

        // Cấu trúc: danh tu - dong tu - tinh tu (noun-verb-adjective)
        $w1 = $nouns[array_rand($nouns)];
        $w2 = $verbs[array_rand($verbs)];
        $w3 = $adjectives[array_rand($adjectives)];

        $parts = [self::normalize($w1), self::normalize($w2), self::normalize($w3)];
        return implode('-', $parts);
    }

    public static function normalize(string $word): string
    {
        $word = strtolower($word);
        // keep a-z 0-9, replace others with '-'
        $word = preg_replace('/[^a-z0-9]+/i', '-', $word) ?? '';
        $word = trim($word, '-');
        return $word;
    }
}


