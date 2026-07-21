<?php

namespace App\View\Composers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;

class PublicLayoutComposer
{
    public function compose(View $view): void
    {
        $sidebarCategories = Category::query()
            ->withCount('posts')
            ->having('posts_count', '>', 0)
            ->orderByDesc('posts_count')
            ->limit(12)
            ->get();

        $sidebarPopularPosts = Post::query()
            ->withCount('likedUsers')
            ->orderByDesc('liked_users_count')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $sidebarRecentPosts = Post::query()
            ->latest()
            ->limit(5)
            ->get();

        $view->with([
            'sidebarCategories'   => $sidebarCategories,
            'sidebarPopularPosts' => $sidebarPopularPosts,
            'sidebarRecentPosts'  => $sidebarRecentPosts,
        ]);
    }
}
