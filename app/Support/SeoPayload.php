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
            'title'       => 'Über uns – Umzugland.at Ratgeber',
            'description' => 'Erfahren Sie mehr über den Umzugland.at Ratgeber für Umzug, Räumung und Leben in Wien.',
            'keywords'    => 'Umzugland, Umzug Wien, Räumung Ratgeber, Über uns',
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
            $payload['robots'] = 'noindex, nofollow';
            $payload['image'] = null; // do not expose impressum image in OG/schema
        }

        return $payload;
    }

    public static function forContact(): array
    {
        return [
            'title'       => 'Kontakt – Umzugland.at',
            'description' => 'Kontaktieren Sie das Umzugland.at Team für Fragen zu Umzug, Räumung und Entrümpelung in Wien.',
            'keywords'    => 'Kontakt Umzug Wien, Umzugland Kontakt, Räumung Anfrage',
            'canonical'   => route('contact.index'),
        ];
    }
}
