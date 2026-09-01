<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Support\SeoPayload;
use App\Support\SchemaMarkup;
use App\Support\StructuredData;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function showHomepage(ViewFactory $view_factory): View
    {
        $gridPostsPerPage = 8;

        $latest_post = Post::query()
            ->publiclyVisible()
            ->with('categories')
            ->orderByDesc('published_at')
            ->paginate($gridPostsPerPage + 1);

        $liked_posts = Post::query()
            ->publiclyVisible()
            ->with('categories')
            ->withCount('likedUsers')
            ->orderByDesc('liked_users_count')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $seo = SeoPayload::forHomepage();

        $structuredData = [
            StructuredData::website(),
            StructuredData::webPage($seo['title'], $seo['description'], $seo['canonical']),
        ];

        if ($latest_post->count() > 0) {
            $structuredData[] = StructuredData::itemListFromPosts(
                $latest_post->getCollection(),
                $seo['canonical'],
                'Aktuelle Ratgeber auf Umzugland.at'
            );
        }

        return $view_factory->make('post.index', [
            'posts'          => $latest_post,
            'likedPosts'     => $liked_posts,
            'seo'            => $seo,
            'structuredData' => $structuredData,
        ]);
    }

    public function show(Post $post, Request $request, ViewFactory $view_factory)
    {
        $post->load(['categories', 'tags']);

        $date = Carbon::parse($post->displayDate());
        $tags = $post->tags;

        $categoryIds = $post->categories->pluck('id');
        $tagIds = $post->tags->pluck('id');

        $relatedPosts = Post::query()
            ->publiclyVisible()
            ->with('categories')
            ->where('id', '!=', $post->id)
            ->when(
                $tagIds->isNotEmpty() || $categoryIds->isNotEmpty(),
                function ($query) use ($tagIds, $categoryIds): void {
                    $query->where(function ($inner) use ($tagIds, $categoryIds): void {
                        if ($categoryIds->isNotEmpty()) {
                            $inner->whereHas(
                                'categories',
                                fn ($categoryQuery) => $categoryQuery->whereIn('categories.id', $categoryIds)
                            );
                        }

                        if ($tagIds->isNotEmpty()) {
                            $inner->orWhereHas(
                                'tags',
                                fn ($tagQuery) => $tagQuery->whereIn('tags.id', $tagIds)
                            );
                        }
                    });
                }
            )
            ->withCount([
                'tags as shared_tags_count' => fn ($query) => $query->whereIn('tags.id', $tagIds),
                'categories as shared_categories_count' => fn ($query) => $query->whereIn('categories.id', $categoryIds),
            ])
            ->orderByDesc('shared_tags_count')
            ->orderByDesc('shared_categories_count')
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        $breadcrumbs = [
            ['label' => 'Startseite', 'url' => route('main.index')],
        ];

        if ($primaryCategory = $post->categories->first()) {
            $breadcrumbs[] = [
                'label' => $primaryCategory->title,
                'url'   => route('category.post.index', $primaryCategory),
            ];
        }

        $breadcrumbs[] = ['label' => $post->title, 'url' => null];

        $seo = SeoPayload::forPost($post);

        $structuredData = [
            StructuredData::webPage($seo['title'], $seo['description'], $seo['canonical'], 'WebPage'),
        ];

        if (! SchemaMarkup::containsArticleType($post->schema_json)) {
            array_unshift($structuredData, StructuredData::article($post));
        }

        return $view_factory->make('post.show', [
            'post'           => $post,
            'relatedPosts'   => $relatedPosts,
            'date'           => $date,
            'tags'           => $tags,
            'breadcrumbs'    => $breadcrumbs,
            'seo'            => $seo,
            'structuredData' => $structuredData,
            'customJsonLd'   => SchemaMarkup::toSafeScript($post->schema_json),
        ]);
    }

    public function byCategory(Category $category, ViewFactory $view_factory)
    {
        $posts = Post::query()
            ->publiclyVisible()
            ->with('categories')
            ->whereHas('categories', fn ($query) => $query->where('categories.id', $category->id))
            ->orderByDesc('published_at')
            ->paginate(10);

        return $view_factory->make('post.index', ['posts' => $posts, 'category' => $category]);
    }

    public function search(Request $request, ViewFactory $view_factory)
    {
        $q = $request->get('q');

        $posts = Post::query()
            ->publiclyVisible()
            ->orderByDesc('published_at')
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            })
            ->paginate(10);

        return $view_factory->make('post.search', ['posts' => $posts]);
    }
}
