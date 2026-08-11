<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $sitemapUrl = config('seo.site_url') . '/sitemap.xml';

        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /personal',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /password',
            'Disallow: /blog/*/comments',
            'Disallow: /blog/*/likes',
            'Disallow: /placeholder',
            '',
            'Sitemap: ' . $sitemapUrl,
        ];

        return response(implode(PHP_EOL, $lines), 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
