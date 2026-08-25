@php
    use App\Models\Post;

    $currentStatus = old('status', isset($post) ? $post->status : Post::STATUS_SCHEDULED);
    $publishedAtValue = old(
        'published_at',
        isset($post) && $post->published_at
            ? $post->published_at->timezone(\App\Support\PublishQueue::timezone())->format('Y-m-d\TH:i')
            : ''
    );
@endphp

<div class="card mb-4 border-secondary">
    <div class="card-header bg-light">
        <strong>Veröffentlichung & Warteschlange</strong>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="status">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="{{ Post::STATUS_SCHEDULED }}" @selected($currentStatus === Post::STATUS_SCHEDULED)>
                        Geplant / Warteschlange
                    </option>
                    <option value="{{ Post::STATUS_PUBLISHED }}" @selected($currentStatus === Post::STATUS_PUBLISHED)>
                        Sofort veröffentlichen
                    </option>
                    <option value="{{ Post::STATUS_DRAFT }}" @selected($currentStatus === Post::STATUS_DRAFT)>
                        Entwurf
                    </option>
                </select>
                @error('status')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="published_at">Veröffentlichungsdatum (optional)</label>
                <input
                    type="datetime-local"
                    name="published_at"
                    id="published_at"
                    class="form-control"
                    value="{{ $publishedAtValue }}"
                >
                <div class="form-text">
                    Wenn leer, wird automatisch ein Warteschlangen-Slot zugewiesen ({{ config('publish_queue.posts_per_day') }} Beiträge pro Tag).
                </div>
                @error('published_at')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            @if(isset($post) && $post->status === Post::STATUS_SCHEDULED && $post->published_at)
                <div class="col-md-4">
                    <label class="form-label">Warteschlangen-Info</label>
                    <p class="mb-1">
                        <span class="badge bg-info text-dark">{{ $post->published_at->timezone(\App\Support\PublishQueue::timezone())->format('d.m.Y H:i') }}</span>
                    </p>
                    @if($countdown = $post->publishCountdown())
                        <p class="text-muted small mb-0">{{ $countdown }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
