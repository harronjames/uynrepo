<?php

namespace App\View\Composers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;

class PublicLayoutComposer
{
    public function compose(View $view): void
    {
        $publishedScope = fn ($query) => $query->publiclyVisible();

        $sidebarCategories = Category::query()
            ->withCount(['posts' => $publishedScope])
            ->having('posts_count', '>', 0)
            ->orderByDesc('posts_count')
            ->limit(12)
            ->get();

        $sidebarPopularPosts = Post::query()
            ->publiclyVisible()
            ->withCount('likedUsers')
            ->orderByDesc('liked_users_count')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $sidebarRecentPosts = Post::query()
            ->publiclyVisible()
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $view->with([
            'sidebarCategories'   => $sidebarCategories,
            'sidebarPopularPosts' => $sidebarPopularPosts,
            'sidebarRecentPosts'  => $sidebarRecentPosts,
        ]);
    }
}
