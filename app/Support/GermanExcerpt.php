<?php

namespace App\Support;

class GermanExcerpt
{
    /**
     * Build a German-friendly meta description from HTML, without cutting mid-word.
     */
    public static function fromHtml(string $html, int $maxChars = 150): string
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        if (mb_strlen($text) <= $maxChars) {
            return $text;
        }

        $cut = mb_substr($text, 0, $maxChars);
        $space = mb_strrpos($cut, ' ');

        if ($space !== false && $space >= (int) floor($maxChars * 0.55)) {
            $cut = mb_substr($cut, 0, $space);
        }

        return rtrim($cut, " \t\n\r\0\x0B.,;:–—-") . ' …';
    }
}
