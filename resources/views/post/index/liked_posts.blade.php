@php
    /** @var \App\Models\Post $likedPost */
@endphp

@if($likedPosts->isNotEmpty())
    <section class="mt-5" aria-label="Beliebte Ratgeber">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <h2 class="h4 portal-post-title mb-0">Beliebte Ratgeber</h2>
            <a href="{{ route('category.index') }}" class="portal-inline-link">Mehr Themen</a>
        </div>

        <div class="row g-4">
            @foreach($likedPosts as $likedPost)
                <div class="col-md-4">
                    <article class="portal-post-card h-100 d-flex flex-column">
                        @if($likedPost->preview_image)
                            <a href="{{ route('post.show', $likedPost) }}">
                                <img src="{{ $likedPost->preview_image }}" alt="{{ $likedPost->title }}" loading="lazy">
                            </a>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h3 class="h6 portal-post-title">
                                <a href="{{ route('post.show', $likedPost) }}">{{ $likedPost->title }}</a>
                            </h3>
                            <p class="text-muted small mb-3">{{ $likedPost->shortBody(18) }}</p>
                            <a href="{{ route('post.show', $likedPost) }}" class="portal-inline-link mt-auto">
                                Artikel öffnen
                            </a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </section>
@endif
