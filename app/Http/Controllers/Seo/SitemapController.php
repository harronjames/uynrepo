<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $siteUrl = config('seo.site_url');

        $urls = [
            $this->entry($siteUrl, now(), 'daily', '1.0'),
            $this->entry(route('category.index'), now(), 'weekly', '0.9'),
            $this->entry(route('about.index'), now()->subWeek(), 'monthly', '0.6'),
            $this->entry(route('contact.index'), now()->subWeek(), 'monthly', '0.7'),
            $this->entry(route('impressum.index'), now()->subWeek(), 'yearly', '0.3'),
        ];

        Category::query()
            ->orderByDesc('updated_at')
            ->get(['id', 'slug', 'updated_at'])
            ->each(function (Category $category) use (&$urls): void {
                $urls[] = $this->entry(
                    route('category.post.index', $category),
                    $category->updated_at,
                    'weekly',
                    '0.8'
                );
            });

        Post::query()
            ->orderByDesc('updated_at')
            ->get(['id', 'slug', 'updated_at'])
            ->each(function (Post $post) use (&$urls): void {
                $urls[] = $this->entry(
                    route('post.show', $post),
                    $post->updated_at,
                    'weekly',
                    '0.8'
                );
            });

        $xml = view('seo.sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function entry(string $loc, $lastModified, string $changefreq, string $priority): array
    {
        return [
            'loc'        => $loc,
            'lastmod'    => $lastModified?->toAtomString(),
            'changefreq' => $changefreq,
            'priority'   => $priority,
        ];
    }
}
