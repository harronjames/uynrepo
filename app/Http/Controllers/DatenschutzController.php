<?php

namespace App\Http\Controllers;

use App\Support\SeoPayload;
use App\Support\StructuredData;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;

class DatenschutzController extends Controller
{
    public function __invoke(ViewFactory $view_factory): View
    {
        $seo = SeoPayload::forDatenschutz();

        return $view_factory->make('datenschutz.index', [
            'seo'         => $seo,
            'breadcrumbs' => [
                ['label' => 'Startseite', 'url' => route('main.index')],
                ['label' => 'Datenschutzerklärung', 'url' => null],
            ],
            'structuredData' => [
                StructuredData::webPage($seo['title'], $seo['description'], $seo['canonical'], 'WebPage'),
            ],
        ]);
    }
}
