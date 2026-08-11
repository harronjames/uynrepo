<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Collection;

class StructuredData
{
    public static function organization(): array
    {
        $siteUrl = config('seo.site_url');

        return [
            '@type'       => 'Organization',
            '@id'         => $siteUrl . '#organization',
            'name'        => config('seo.organization.name'),
            'url'         => $siteUrl,
            'email'       => config('seo.organization.email'),
            'description' => config('seo.organization.description'),
            'areaServed'  => [
                '@type' => 'Country',
                'name'  => 'Österreich',
            ],
        ];
    }

    public static function localBusiness(): array
    {
        // Kept for backwards compatibility; portal is informational only (no service offers).
        return self::organization();
    }

    public static function website(): array
    {
        $siteUrl = config('seo.site_url');

        return [
            '@type'           => 'WebSite',
            '@id'             => $siteUrl . '#website',
            'name'            => config('seo.site_name'),
            'url'             => $siteUrl,
            'description'     => config('seo.organization.description'),
            'inLanguage'      => config('seo.organization.locale'),
            'publisher'       => ['@id' => $siteUrl . '#organization'],
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => $siteUrl . '/category?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    public static function webPage(string $name, string $description, string $url, ?string $type = 'WebPage'): array
    {
        $siteUrl = config('seo.site_url');

        return [
            '@type'       => $type,
            '@id'         => $url . '#webpage',
            'name'        => $name,
            'description' => $description,
            'url'         => $url,
            'inLanguage'  => config('seo.organization.locale'),
            'isPartOf'    => ['@id' => $siteUrl . '#website'],
            'publisher'   => ['@id' => $siteUrl . '#organization'],
        ];
    }

    public static function article(Post $post): array
    {
        $url = route('post.show', $post);

        $schema = [
            '@type'            => 'Article',
            '@id'              => $url . '#article',
            'headline'         => $post->title,
            'description'      => $post->seoDescription(),
            'url'              => $url,
            'datePublished'    => $post->created_at?->toIso8601String(),
            'dateModified'     => $post->updated_at?->toIso8601String(),
            'inLanguage'       => config('seo.organization.locale'),
            'author'           => ['@id' => config('seo.site_url') . '#organization'],
            'publisher'        => ['@id' => config('seo.site_url') . '#organization'],
            'mainEntityOfPage' => ['@id' => $url . '#webpage'],
            'isPartOf'         => ['@id' => config('seo.site_url') . '#website'],
        ];

        if ($image = $post->main_image ?: $post->preview_image) {
            $schema['image'] = [$image];
        }

        if ($post->categories->isNotEmpty()) {
            $schema['articleSection'] = $post->categories->pluck('title')->all();
            $schema['keywords']       = $post->categories->pluck('title')->implode(', ');
        }

        return $schema;
    }

    public static function collectionPage(string $name, string $description, string $url): array
    {
        return self::webPage($name, $description, $url, 'CollectionPage');
    }

    public static function breadcrumbList(array $breadcrumbs): array
    {
        $items = [];

        foreach ($breadcrumbs as $index => $crumb) {
            $item = [
                '@type'    => 'ListItem',
                'position' => $index + 1,
                'name'     => $crumb['label'],
            ];

            if (! empty($crumb['url'])) {
                $item['item'] = $crumb['url'];
            }

            $items[] = $item;
        }

        return [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    public static function itemListFromPosts(Collection $posts, string $pageUrl, string $listName): array
    {
        $items = [];

        foreach ($posts as $index => $post) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $index + 1,
                'url'      => route('post.show', $post),
                'name'     => $post->title,
            ];
        }

        return [
            '@type'           => 'ItemList',
            'name'            => $listName,
            'url'             => $pageUrl,
            'itemListElement' => $items,
        ];
    }

    public static function itemListFromCategories(Collection $categories, string $pageUrl): array
    {
        $items = [];

        foreach ($categories as $index => $category) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $index + 1,
                'url'      => route('category.post.index', $category),
                'name'     => $category->title,
            ];
        }

        return [
            '@type'           => 'ItemList',
            'name'            => 'Themenübersicht Umzugland.at',
            'url'             => $pageUrl,
            'itemListElement' => $items,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $nodes
     */
    public static function graph(array $nodes): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph'   => array_values($nodes),
        ];
    }

    public static function toJson(array $nodes): string
    {
        return json_encode(
            self::graph($nodes),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );
    }
}
