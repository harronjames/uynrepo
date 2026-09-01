<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Post;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

class InternalLinker
{
    /** @var list<array{keyword: string, url: string, post_id: int|null}>|null */
    private static ?array $resolvedTargets = null;

    public static function enrich(string $html, Post $current): string
    {
        if (! config('internal_links.enabled', true)) {
            return $html;
        }

        $html = trim($html);

        if ($html === '') {
            return '';
        }

        $targets = self::targets($current);

        if ($targets === []) {
            return $html;
        }

        $maxLinks = max(1, (int) config('internal_links.max_links_per_post', 6));
        $linkClass = (string) config('internal_links.link_class', 'portal-inline-link');

        $previous = libxml_use_internal_errors(true);

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="internal-link-root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('internal-link-root');

        if (! $root instanceof DOMElement) {
            return $html;
        }

        $remaining = $maxLinks;
        $usedKeywords = [];

        self::walk($document, $root, $targets, $linkClass, $remaining, $usedKeywords);

        $output = '';

        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim($output);
    }

    /**
     * @return list<array{keyword: string, url: string, post_id: int|null}>
     */
    public static function targets(Post $current): array
    {
        return array_values(array_filter(
            self::allTargets(),
            fn (array $target): bool => ($target['post_id'] ?? null) !== $current->id
        ));
    }

    /**
     * @return list<array{keyword: string, url: string, post_id: int|null}>
     */
    private static function allTargets(): array
    {
        if (self::$resolvedTargets !== null) {
            return self::$resolvedTargets;
        }

        $posts = Post::query()
            ->publiclyVisible()
            ->orderByDesc('published_at')
            ->get(['id', 'title', 'slug']);

        $categories = Category::query()
            ->orderBy('title')
            ->get(['id', 'title', 'slug']);

        $targets = [];
        $phrases = config('internal_links.phrases', []);

        if (! is_array($phrases)) {
            $phrases = [];
        }

        foreach ($phrases as $phrase) {
            if (! is_array($phrase) || empty($phrase['text'])) {
                continue;
            }

            $text = (string) $phrase['text'];
            $url = null;
            $postId = null;

            if (! empty($phrase['post_title_contains'])) {
                $needle = (string) $phrase['post_title_contains'];
                $match = $posts->first(fn (Post $post): bool => str_contains($post->title, $needle));

                if ($match instanceof Post) {
                    $url = route('post.show', $match);
                    $postId = $match->id;
                }
            }

            if ($url === null && ! empty($phrase['category_title_contains'])) {
                $needle = (string) $phrase['category_title_contains'];
                $match = $categories->first(fn (Category $category): bool => str_contains($category->title, $needle));

                if ($match instanceof Category) {
                    $url = route('category.post.index', $match);
                }
            }

            if ($url !== null) {
                $targets[] = ['keyword' => $text, 'url' => $url, 'post_id' => $postId];
            }
        }

        usort($targets, fn (array $a, array $b): int => mb_strlen($b['keyword']) <=> mb_strlen($a['keyword']));

        self::$resolvedTargets = $targets;

        return $targets;
    }

    /**
     * @param  list<array{keyword: string, url: string, post_id: int|null}>  $targets
     * @param  array<string, bool>  $usedKeywords
     */
    private static function walk(
        DOMDocument $document,
        DOMNode $node,
        array $targets,
        string $linkClass,
        int &$remaining,
        array &$usedKeywords
    ): void {
        if ($remaining <= 0) {
            return;
        }

        if ($node instanceof DOMText) {
            self::linkTextNode($document, $node, $targets, $linkClass, $remaining, $usedKeywords);

            return;
        }

        if (! $node instanceof DOMElement) {
            return;
        }

        foreach (iterator_to_array($node->childNodes) as $child) {
            self::walk($document, $child, $targets, $linkClass, $remaining, $usedKeywords);
        }
    }

    /**
     * @param  list<array{keyword: string, url: string, post_id: int|null}>  $targets
     * @param  array<string, bool>  $usedKeywords
     */
    private static function linkTextNode(
        DOMDocument $document,
        DOMText $node,
        array $targets,
        string $linkClass,
        int &$remaining,
        array &$usedKeywords
    ): void {
        if ($remaining <= 0 || self::hasSkippedAncestor($node)) {
            return;
        }

        $text = $node->textContent ?? '';

        if (trim($text) === '') {
            return;
        }

        foreach ($targets as $target) {
            if ($remaining <= 0) {
                return;
            }

            $keyword = $target['keyword'];

            if (isset($usedKeywords[$keyword])) {
                continue;
            }

            $pos = mb_stripos($text, $keyword);

            if ($pos === false) {
                continue;
            }

            $before = mb_substr($text, 0, $pos);
            $match = mb_substr($text, $pos, mb_strlen($keyword));
            $after = mb_substr($text, $pos + mb_strlen($keyword));

            $parent = $node->parentNode;

            if (! $parent instanceof DOMNode) {
                return;
            }

            if ($before !== '') {
                $parent->insertBefore($document->createTextNode($before), $node);
            }

            $anchor = $document->createElement('a');
            $anchor->setAttribute('href', $target['url']);
            $anchor->setAttribute('class', $linkClass);
            $anchor->appendChild($document->createTextNode($match));
            $parent->insertBefore($anchor, $node);

            if ($after !== '') {
                $afterNode = $document->createTextNode($after);
                $parent->insertBefore($afterNode, $node);
                self::linkTextNode($document, $afterNode, $targets, $linkClass, $remaining, $usedKeywords);
            }

            $parent->removeChild($node);
            $usedKeywords[$keyword] = true;
            $remaining--;

            return;
        }
    }

    private static function hasSkippedAncestor(DOMText $node): bool
    {
        $skipTags = config('internal_links.skip_ancestor_tags', []);

        if (! is_array($skipTags)) {
            $skipTags = [];
        }

        $current = $node->parentNode;

        while ($current instanceof DOMElement) {
            if (in_array(strtolower($current->tagName), $skipTags, true)) {
                return true;
            }

            $current = $current->parentNode;
        }

        return false;
    }
}
