<?php

namespace App\Http\Controllers\Admin\Post;

use App\Models\Post;
use App\Support\PublishQueue;
use Illuminate\Http\RedirectResponse;

class PublishNowController extends BaseController
{
    public function __invoke(Post $post): RedirectResponse
    {
        PublishQueue::publishNow($post);

        return redirect()
            ->route('admin.post.index', ['status' => 'scheduled'])
            ->with('success', "“{$post->title}” sofort veröffentlicht.");
    }
}
