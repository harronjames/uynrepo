<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Response;

class RssController extends Controller
{
    public function __invoke(): Response
    {
        $siteUrl = config('seo.site_url');
        $siteName = config('seo.site_name', 'Umzugland.at');

        $posts = Post::query()
            ->publiclyVisible()
            ->orderByDesc('published_at')
            ->limit(50)
            ->get(['id', 'title', 'slug', 'meta_description', 'content', 'published_at', 'updated_at']);

        $xml = view('seo.rss', [
            'siteUrl'  => $siteUrl,
            'siteName' => $siteName,
            'posts'    => $posts,
        ])->render();

        return response($xml, 200)->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
