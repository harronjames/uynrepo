<?php

namespace App\Http\Controllers;

use App\Support\SeoPayload;
use App\Support\StructuredData;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;

class ContactsController extends Controller
{
    public function showContacts(ViewFactory $view_factory): View
    {
        $seo = SeoPayload::forContact();

        return $view_factory->make('contact.index', [
            'seo'         => $seo,
            'breadcrumbs' => [
                ['label' => 'Startseite', 'url' => route('main.index')],
                ['label' => 'Kontakt', 'url' => null],
            ],
            'structuredData' => [
                StructuredData::webPage($seo['title'], $seo['description'], $seo['canonical'], 'ContactPage'),
            ],
        ]);
    }
}
