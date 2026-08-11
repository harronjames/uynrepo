<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\SeoPayload;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Response;

class ImpressumController extends Controller
{
    public function __invoke(ViewFactory $view_factory): Response
    {
        $page = Page::query()->where('slug', 'impressum')->firstOrFail();
        $seo = SeoPayload::forPage($page);

        $html = $view_factory->make('impressum.index', [
            'page' => $page,
            'seo'  => $seo,
        ])->render();

        return response($html, 200)
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function image(): Response
    {
        $page = Page::query()->where('slug', 'impressum')->firstOrFail();

        if (! $page->image) {
            abort(404);
        }

        // $page->image genelde "/storage/..." olarak saklanıyor; bazen de tam URL olabiliyor.
        // parse_url() relative path için null döndürebileceğinden daha güvenli normalize ediyoruz.
        $stored = (string) $page->image;
        $path = $stored;

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $path = (string) (parse_url($path, PHP_URL_PATH) ?? '');
        }

        $path = ltrim($path, '/'); // storage/... şekline getir

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        $absolute = storage_path('app/public/' . $path);

        if (! is_file($absolute)) {
            abort(404);
        }

        return response()->file($absolute, [
            'X-Robots-Tag'  => 'noindex, nofollow',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }
}
