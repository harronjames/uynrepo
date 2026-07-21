<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\SeoPayload;
use App\Support\StructuredData;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;

class AboutController extends Controller
{
    public function showAbout(ViewFactory $view_factory): View
    {
        $page = Page::query()->where('slug', 'about')->firstOrFail();
        $seo = SeoPayload::forPage($page);

        return $view_factory->make('about.index', [
            'page'        => $page,
            'seo'         => $seo,
            'breadcrumbs' => [
                ['label' => 'Startseite', 'url' => route('main.index')],
                ['label' => $page->title, 'url' => null],
            ],
            'structuredData' => [
                StructuredData::webPage($seo['title'], $seo['description'], $seo['canonical'], 'AboutPage'),
            ],
        ]);
    }
}
