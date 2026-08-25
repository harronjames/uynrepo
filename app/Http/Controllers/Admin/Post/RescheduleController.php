<?php

namespace App\Http\Controllers\Admin\Post;

use App\Models\Post;
use App\Support\PublishQueue;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RescheduleController extends BaseController
{
    public function __invoke(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'published_at' => 'required|date',
            'status'       => 'nullable|in:scheduled,published,draft',
        ]);

        $at = Carbon::parse($validated['published_at'], PublishQueue::timezone());
        $status = $validated['status'] ?? Post::STATUS_SCHEDULED;

        if ($status === Post::STATUS_PUBLISHED || $at->lessThanOrEqualTo(now())) {
            $post->status = Post::STATUS_PUBLISHED;
            $post->published_at = $at;
            $post->queue_position = null;
        } elseif ($status === Post::STATUS_DRAFT) {
            $post->status = Post::STATUS_DRAFT;
            $post->published_at = null;
            $post->queue_position = null;
        } else {
            $post->status = Post::STATUS_SCHEDULED;
            $post->published_at = $at;
            $post->queue_position = $post->queue_position ?? PublishQueue::nextQueuePosition();
        }

        $post->save();

        return redirect()
            ->route('admin.post.index', ['status' => $post->status === Post::STATUS_PUBLISHED ? 'published' : 'scheduled'])
            ->with('success', "Veröffentlichungsdatum für „{$post->title}“ aktualisiert.");
    }
}
