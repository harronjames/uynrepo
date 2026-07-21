<?php

namespace App\Http\Controllers\Admin\Page;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Contracts\View\Factory as ViewFactory;

class EditImpressumController extends Controller
{
    public function __invoke(ViewFactory $view_factory)
    {
        $page = Page::query()->firstOrCreate(
            ['slug' => 'impressum'],
            [
                'title'            => 'Impressum',
                'content'          => '',
                'image'            => null,
                'meta_title'       => 'Impressum – Umzugland.at',
                'meta_description' => 'Impressum von Umzugland.at',
                'meta_keywords'    => 'Impressum, Umzugland.at',
            ]
        );

        return $view_factory->make('admin.page.impressum', [
            'page' => $page,
        ]);
    }
}
