<?php

namespace App\Http\Controllers\Admin\Post;

use App\Models\Post;
use App\Support\PublishQueue;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    public function __invoke(Request $request, ViewFactory $view_factory)
    {
        $status = (string) $request->query('status', 'all');

        $query = Post::query();

        match ($status) {
            Post::STATUS_PUBLISHED => $query->publishedStatus()->orderByDesc('published_at'),
            Post::STATUS_SCHEDULED => $query->scheduled()->orderBy('published_at'),
            Post::STATUS_DRAFT     => $query->draft()->orderByDesc('updated_at'),
            default                => $query->orderByRaw('CASE WHEN status = ? THEN 0 WHEN status = ? THEN 1 ELSE 2 END', [
                Post::STATUS_SCHEDULED,
                Post::STATUS_PUBLISHED,
            ])->orderBy('published_at')->orderByDesc('id'),
        };

        $posts = $query->get();

        $queuePosts = Post::query()
            ->scheduled()
            ->where('published_at', '>', PublishQueue::publicationCutoff())
            ->orderBy('published_at')
            ->get();

        $counts = [
            'all'       => Post::query()->count(),
            'published' => Post::query()->publishedStatus()->count(),
            'scheduled' => Post::query()->scheduled()->count(),
            'draft'     => Post::query()->draft()->count(),
        ];

        return $view_factory->make('admin.post.index', [
            'posts'      => $posts,
            'queuePosts' => $queuePosts,
            'status'     => $status,
            'counts'     => $counts,
        ]);
    }
}
