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
        $page = Page::query()->firstOrCreate(
            ['slug' => 'about'],
            [
                'title'            => 'Über uns',
                'content'          => '',
                'meta_title'       => 'Über uns – Umzugland.at | Unabhängiger Ratgeber',
                'meta_description' => 'Umzugland.at ist ein unabhängiges Informationsportal zu Umzug, Entrümpelung und Leben in Wien – ohne Verkauf oder Dienstleistungsangebot.',
                'meta_keywords'    => 'Umzugland, Ratgeber Wien, Über uns, Umzug Tipps',
            ]
        );

        // Keep SEO metadata informational even if older DB content still exists.
        $page->fill([
            'title'            => 'Über uns',
            'meta_title'       => 'Über uns – Umzugland.at | Unabhängiger Ratgeber',
            'meta_description' => 'Umzugland.at ist ein unabhängiges Informationsportal zu Umzug, Entrümpelung und Leben in Wien – ohne Verkauf oder Dienstleistungsangebot.',
            'meta_keywords'    => 'Umzugland, Ratgeber Wien, Über uns, Umzug Tipps',
        ]);

        $seo = SeoPayload::forPage($page);

        return $view_factory->make('about.index', [
            'page'        => $page,
            'seo'         => $seo,
            'breadcrumbs' => [
                ['label' => 'Startseite', 'url' => route('main.index')],
                ['label' => 'Über uns', 'url' => null],
            ],
            'structuredData' => [
                StructuredData::webPage($seo['title'], $seo['description'], $seo['canonical'], 'AboutPage'),
            ],
        ]);
    }
}
