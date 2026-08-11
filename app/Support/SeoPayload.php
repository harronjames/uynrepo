<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;

class SeoPayload
{
    public static function forHomepage(): array
    {
        return [
            'title'       => 'Umzugland.at – Umzug, Räumung & Leben in Wien',
            'description' => 'Ratgeber und Tipps zu Umzug, Entrümpelung, Räumung und Leben in Wien und Österreich.',
            'keywords'    => 'Umzug Wien, Räumung, Entrümpelung, Umzugstipps, Leben in Wien',
            'canonical'   => route('main.index'),
        ];
    }

    public static function forPost(Post $post, ?string $canonical = null): array
    {
        return array_merge($post->toSeoPayload($canonical ?? route('post.show', $post)), [
            'type'  => 'article',
            'image' => $post->main_image ?: $post->preview_image,
        ]);
    }

    public static function forCategory(Category $category, ?string $canonical = null): array
    {
        $canonical ??= route('category.post.index', $category);

        if (trim((string) ($category->meta_description ?? '')) === '') {
            return [
                'title'       => $category->seoTitle(),
                'description' => "Ratgeber und Artikel zur Kategorie {$category->title} – Umzug, Räumung und Leben in Wien.",
                'keywords'    => $category->seoKeywords(),
                'canonical'   => $canonical,
            ];
        }

        return $category->toSeoPayload($canonical);
    }

    public static function forCategoryIndex(): array
    {
        return [
            'title'       => 'Kategorien – Umzugland.at',
            'description' => 'Alle Themen zu Umzug, Räumung, Entrümpelung und Leben in Wien auf einen Blick.',
            'keywords'    => 'Umzug Kategorien, Räumung Wien, Entrümpelung, Leben in Wien',
            'canonical'   => route('category.index'),
        ];
    }

    public static function forAbout(): array
    {
        $page = Page::query()->where('slug', 'about')->first();

        if ($page) {
            return self::forPage($page);
        }

        return [
            'title'       => 'Über uns – Umzugland.at | Unabhängiger Ratgeber',
            'description' => 'Umzugland.at ist ein unabhängiges Informationsportal zu Umzug, Entrümpelung und Leben in Wien – ohne Verkauf oder Dienstleistungsangebot.',
            'keywords'    => 'Umzugland, Ratgeber Wien, Über uns, Umzug Tipps',
            'canonical'   => route('about.index'),
        ];
    }

    public static function forPage(Page $page, ?string $canonical = null): array
    {
        $canonical ??= match ($page->slug) {
            'about'     => route('about.index'),
            'impressum' => route('impressum.index'),
            default     => url('/' . ltrim($page->slug, '/')),
        };

        $payload = $page->toSeoPayload($canonical);

        if ($page->slug === 'impressum') {
            $payload['image'] = null; // do not expose impressum image in OG/schema
        }

        return $payload;
    }

    public static function forContact(): array
    {
        return [
            'title'       => 'Kontakt – Umzugland.at',
            'description' => 'Fragen zu unseren Ratgebern? Schreiben Sie an info@umzugland.at. Umzugland.at ist ein Informationsportal ohne Verkauf oder Dienstleistungen.',
            'keywords'    => 'Kontakt Umzugland, info@umzugland.at, Ratgeber Wien Fragen',
            'canonical'   => route('contact.index'),
        ];
    }
}
