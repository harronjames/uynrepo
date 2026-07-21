<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Support\SeoPayload;
use App\Support\StructuredData;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function showHomepage(ViewFactory $view_factory): View
    {
        /** @phpstan-ignore-next-line */
        $latest_post = Post::with('categories')->paginate(6);
        /** @phpstan-ignore-next-line */
        $liked_posts = Post::with('categories')->withCount('likedUsers')->orderBy('liked_users_count', 'desc')->get()->take(3);

        // todo Show recent categories with their latest posts

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

        $date = Carbon::parse($post->created_at);
        $tags = $post->tags;

        $categoryIds = $post->categories->pluck('id');

        $relatedPosts = Post::query()
            ->with('categories')
            ->where('id', '!=', $post->id)
            ->when(
                $categoryIds->isNotEmpty(),
                fn ($query) => $query->whereHas(
                    'categories',
                    fn ($categoryQuery) => $categoryQuery->whereIn('categories.id', $categoryIds)
                )
            )
            ->latest()
            ->limit(3)
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

        return $view_factory->make('post.show', [
            'post'         => $post,
            'relatedPosts' => $relatedPosts,
            'date'         => $date,
            'tags'         => $tags,
            'breadcrumbs'  => $breadcrumbs,
            'seo'          => $seo,
            'structuredData' => [
                StructuredData::article($post),
                StructuredData::webPage($seo['title'], $seo['description'], $seo['canonical'], 'WebPage'),
            ],
        ]);
    }

    public function byCategory(Category $category, ViewFactory $view_factory)
    {
        $posts = Post::query()
            ->join('category_post', 'posts.id', '=', 'category_post.post_id')
            ->where('category_post.category_id', '=', $category->id)
            ->whereDate('published_at', '<=', Carbon::now())
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return $view_factory->make('post.index', ['posts' => $posts, 'category' => $category]);
    }

    public function search(Request $request, ViewFactory $view_factory)
    {
        $q = $request->get('q');

        $posts = Post::query()
            ->whereDate('published_at', '<=', Carbon::now())
            ->orderBy('published_at', 'desc')
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('body', 'like', "%{$q}%");
            })
            ->paginate(10);

        return $view_factory->make('post.search', ['posts' => $posts]);
    }
}
