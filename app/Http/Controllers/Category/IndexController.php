<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\SeoPayload;
use App\Support\StructuredData;
use Illuminate\Contracts\View\Factory as ViewFactory;

class IndexController extends Controller
{
    public function __invoke(ViewFactory $view_factory)
    {
        $categories = Category::all();

        $seo = SeoPayload::forCategoryIndex();

        $structuredData = [
            StructuredData::collectionPage($seo['title'], $seo['description'], $seo['canonical']),
        ];

        if ($categories->isNotEmpty()) {
            $structuredData[] = StructuredData::itemListFromCategories($categories, $seo['canonical']);
        }

        return $view_factory->make('category.index', [
            'categories'  => $categories,
            'seo'         => $seo,
            'breadcrumbs' => [
                ['label' => 'Startseite', 'url' => route('main.index')],
                ['label' => 'Themen', 'url' => null],
            ],
            'structuredData' => $structuredData,
        ]);
    }
}
