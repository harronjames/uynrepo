<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class HtmlSanitizer
{
    /** @var list<string> */
    private const ALLOWED_TAGS = [
        'p', 'br', 'h1', 'h2', 'h3', 'h4',
        'ul', 'ol', 'li',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'caption',
        'blockquote', 'pre', 'code',
        'a', 'strong', 'em', 'b', 'i', 'u', 's', 'sub', 'sup',
        'figure', 'figcaption', 'img', 'hr', 'span',
    ];

    /** @var array<string, list<string>> */
    private const ALLOWED_ATTR = [
        'a'  => ['href', 'title', 'rel', 'target'],
        'img' => ['src', 'alt', 'width', 'height', 'loading', 'decoding'],
        'td' => ['colspan', 'rowspan'],
        'th' => ['colspan', 'rowspan'],
        '*'  => ['class'],
    ];

    /** @var list<string> */
    private const ALLOWED_REL = ['nofollow', 'noopener', 'noreferrer', 'sponsored', 'ugc'];

    public static function clean(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        $previous = libxml_use_internal_errors(true);

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="sanitizer-root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('sanitizer-root');

        if (! $root instanceof DOMElement) {
            return '';
        }

        self::scrubNode($root);

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim($output);
    }

    private static function scrubNode(DOMNode $node): void
    {
        $remove = [];

        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);

                if ($tag === 'script' || $tag === 'style' || $tag === 'iframe' || $tag === 'object' || $tag === 'embed' || $tag === 'form' || $tag === 'input') {
                    $remove[] = $child;
                    continue;
                }

                if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $remove[] = $child;
                    continue;
                }

                self::scrubAttributes($child);
                self::scrubNode($child);
            } elseif ($child->nodeType === XML_COMMENT_NODE) {
                $remove[] = $child;
            }
        }

        foreach ($remove as $dead) {
            $dead->parentNode?->removeChild($dead);
        }
    }

    private static function scrubAttributes(DOMElement $element): void
    {
        $tag = strtolower($element->tagName);
        $allowed = array_merge(self::ALLOWED_ATTR['*'], self::ALLOWED_ATTR[$tag] ?? []);

        $remove = [];

        foreach (iterator_to_array($element->attributes ?? []) as $attribute) {
            $name = strtolower($attribute->name);
            $value = trim($attribute->value);

            if (str_starts_with($name, 'on') || ! in_array($name, $allowed, true)) {
                $remove[] = $name;
                continue;
            }

            if ($name === 'href' || $name === 'src') {
                if (! self::isSafeUrl($value, $name === 'src')) {
                    $remove[] = $name;
                    continue;
                }
            }

            if ($name === 'target' && $value !== '_blank' && $value !== '_self') {
                $remove[] = $name;
                continue;
            }

            if ($name === 'rel') {
                $tokens = preg_split('/\s+/', strtolower($value)) ?: [];
                $tokens = array_values(array_intersect($tokens, self::ALLOWED_REL));
                $element->setAttribute('rel', implode(' ', $tokens));
            }

            if ($name === 'class') {
                $class = preg_replace('/[^a-zA-Z0-9 _\-]/', '', $value) ?? '';
                if ($class === '') {
                    $remove[] = $name;
                } else {
                    $element->setAttribute('class', $class);
                }
            }
        }

        foreach ($remove as $name) {
            $element->removeAttribute($name);
        }

        if ($tag === 'a') {
            $target = $element->getAttribute('target');
            $relTokens = preg_split('/\s+/', strtolower($element->getAttribute('rel'))) ?: [];
            $relTokens = array_values(array_filter($relTokens));

            if ($target === '_blank') {
                $relTokens = array_values(array_unique(array_merge($relTokens, ['noopener', 'noreferrer'])));
            }

            if ($relTokens === []) {
                $element->removeAttribute('rel');
            } else {
                $element->setAttribute('rel', implode(' ', $relTokens));
            }
        }

        if ($tag === 'img' && $element->getAttribute('alt') === '') {
            $element->setAttribute('alt', '');
        }
    }

    private static function isSafeUrl(string $url, bool $isImage): bool
    {
        if ($url === '' || str_starts_with($url, '#')) {
            return ! $isImage;
        }

        $url = str_replace(['\\', "\0", "\r", "\n", "\t"], '', $url);
        $lower = strtolower($url);

        if (str_contains($lower, 'javascript:') || str_contains($lower, 'vbscript:') || str_contains($lower, 'data:')) {
            return false;
        }

        if (str_starts_with($url, '/')) {
            return ! str_starts_with($url, '//');
        }

        if (str_starts_with($lower, 'mailto:') && ! $isImage) {
            return (bool) preg_match('/^mailto:[^\s<>"\']+$/i', $url);
        }

        return (bool) preg_match('#^https?://[^\s<>"\']+$#i', $url);
    }
}
