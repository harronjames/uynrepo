<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\SeoPayload;
use App\Support\StructuredData;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;

class ImpressumController extends Controller
{
    public function __invoke(ViewFactory $view_factory): View
    {
        $page = Page::query()->where('slug', 'impressum')->firstOrFail();
        $seo = SeoPayload::forPage($page);

        return $view_factory->make('impressum.index', [
            'page'        => $page,
            'seo'         => $seo,
            'breadcrumbs' => [
                ['label' => 'Startseite', 'url' => route('main.index')],
                ['label' => $page->title, 'url' => null],
            ],
            'structuredData' => [
                StructuredData::webPage($seo['title'], $seo['description'], $seo['canonical'], 'WebPage'),
            ],
        ]);
    }

    public function image(): \Symfony\Component\HttpFoundation\Response
    {
        $page = Page::query()->where('slug', 'impressum')->firstOrFail();

        if (! $page->image) {
            abort(404);
        }

        $stored = (string) $page->image;
        $path = $stored;

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $path = (string) (parse_url($path, PHP_URL_PATH) ?? '');
        }

        $path = ltrim($path, '/');

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
