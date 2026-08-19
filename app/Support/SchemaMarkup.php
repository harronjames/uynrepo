<?php

namespace App\Support;

use App\Models\Post;
use JsonException;

class SchemaMarkup
{
    public const MAX_BYTES = 32000;

    /**
     * @return array<string, mixed>|null
     */
    public static function decode(?string $json): ?array
    {
        $json = trim((string) $json);

        if ($json === '') {
            return null;
        }

        if (strlen($json) > self::MAX_BYTES) {
            return null;
        }

        try {
            $decoded = json_decode($json, true, 32, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }

        if (! is_array($decoded) || $decoded === []) {
            return null;
        }

        return $decoded;
    }

    public static function isValid(?string $json): bool
    {
        if (trim((string) $json) === '') {
            return true;
        }

        $decoded = self::decode($json);

        return $decoded !== null && self::looksLikeJsonLd($decoded);
    }

    /**
     * Re-encode so the payload is safe to print inside <script type="application/ld+json">.
     */
    public static function toSafeScript(?string $json): ?string
    {
        $decoded = self::decode($json);

        if ($decoded === null || ! self::looksLikeJsonLd($decoded)) {
            return null;
        }

        try {
            return json_encode(
                $decoded,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            return null;
        }
    }

    public static function containsArticleType(?string $json): bool
    {
        $decoded = self::decode($json);

        if ($decoded === null) {
            return false;
        }

        return self::hasType($decoded, ['article', 'blogposting', 'newsarticle']);
    }

    /**
     * @return array<string, mixed>
     */
    public static function blogPostingFor(Post $post): array
    {
        $url = route('post.show', $post);
        $siteUrl = rtrim((string) config('seo.site_url'), '/');
        $image = AbsoluteUrl::from($post->main_image ?: $post->preview_image);

        $schema = [
            '@context'         => 'https://schema.org',
            '@type'            => 'BlogPosting',
            'headline'         => $post->title,
            'description'      => $post->seoDescription(),
            'url'              => $url,
            'mainEntityOfPage' => $url,
            'datePublished'    => $post->created_at?->toIso8601String(),
            'dateModified'     => $post->updated_at?->toIso8601String(),
            'inLanguage'       => config('seo.organization.locale', 'de-AT'),
            'author'           => [
                '@type' => 'Organization',
                'name'  => config('seo.organization.name', 'Umzugland.at'),
                'url'   => $siteUrl,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name'  => config('seo.organization.name', 'Umzugland.at'),
                'url'   => $siteUrl,
            ],
        ];

        if ($image) {
            $schema['image'] = [$image];
        }

        return $schema;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private static function looksLikeJsonLd(array $payload): bool
    {
        if (array_is_list($payload)) {
            foreach ($payload as $item) {
                if (! is_array($item) || $item === [] || ! self::looksLikeJsonLd($item)) {
                    return false;
                }
            }

            return $payload !== [];
        }

        return isset($payload['@type']) || isset($payload['@graph']) || isset($payload['@context']);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  list<string>  $types
     */
    private static function hasType(array $payload, array $types): bool
    {
        if (isset($payload['@type']) && self::typeMatches($payload['@type'], $types)) {
            return true;
        }

        if (isset($payload['@graph']) && is_array($payload['@graph'])) {
            foreach ($payload['@graph'] as $node) {
                if (is_array($node) && self::hasType($node, $types)) {
                    return true;
                }
            }
        }

        if (array_is_list($payload)) {
            foreach ($payload as $node) {
                if (is_array($node) && self::hasType($node, $types)) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function typeMatches(mixed $type, array $types): bool
    {
        $candidates = is_array($type) ? $type : [$type];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && in_array(strtolower($candidate), $types, true)) {
                return true;
            }
        }

        return false;
    }
}
