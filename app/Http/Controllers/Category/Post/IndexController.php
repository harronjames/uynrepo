<?php

namespace App\Http\Controllers\Category\Post;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\SeoPayload;
use App\Support\StructuredData;
use Illuminate\Contracts\View\Factory as ViewFactory;

class IndexController extends Controller
{
    public function __invoke(Category $category, ViewFactory $view_factory)
    {
        $posts = $category->posts()
            ->publiclyVisible()
            ->with('categories')
            ->orderByDesc('published_at')
            ->paginate(6);

        $seo = SeoPayload::forCategory($category);

        $structuredData = [
            StructuredData::collectionPage($seo['title'], $seo['description'], $seo['canonical']),
        ];

        if ($posts->count() > 0) {
            $structuredData[] = StructuredData::itemListFromPosts(
                $posts->getCollection(),
                $seo['canonical'],
                'Artikel in ' . $category->title
            );
        }

        return $view_factory->make('category.post.index', [
            'posts'       => $posts,
            'category'    => $category,
            'seo'         => $seo,
            'breadcrumbs' => [
                ['label' => 'Startseite', 'url' => route('main.index')],
                ['label' => 'Themen', 'url' => route('category.index')],
                ['label' => $category->title, 'url' => null],
            ],
            'structuredData' => $structuredData,
        ]);
    }
}
