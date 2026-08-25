@php
    /** @var \App\Models\Post $post */
@endphp

@if($posts->isNotEmpty())
    @php
        $featuredPost = $posts->first();
        $otherPosts = $posts->slice(1);
    @endphp

    <section class="portal-hero mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <p class="text-uppercase small mb-2 opacity-75">Top-Ratgeber</p>
                <h1 class="mb-3">
                    <a href="{{ route('post.show', $featuredPost) }}" class="text-white text-decoration-none">
                        {{ $featuredPost->title }}
                    </a>
                </h1>
                <p class="mb-4 lead opacity-90">{{ $featuredPost->shortBody(28) }}</p>
                <a href="{{ route('post.show', $featuredPost) }}" class="btn btn-light rounded-pill px-4">
                    Artikel lesen
                </a>
            </div>
            @if($featuredPost->card_image)
                <div class="col-lg-5">
                    <img src="{{ $featuredPost->card_image }}" alt="{{ $featuredPost->title }}" class="img-fluid rounded-4 shadow">
                </div>
            @endif
        </div>
    </section>

    <section aria-label="Weitere Artikel">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <h2 class="h4 portal-post-title mb-0">Aktuelle Ratgeber</h2>
            <a href="{{ route('category.index') }}" class="portal-inline-link">Alle Themen</a>
        </div>

        <div class="row g-4">
            @foreach($otherPosts as $post)
                <div class="col-md-6">
                    <article class="portal-post-card d-flex flex-column">
                        @if($post->card_image)
                            <a href="{{ route('post.show', $post) }}">
                                <img src="{{ $post->card_image }}" alt="{{ $post->title }}" loading="lazy">
                            </a>
                        @endif
                        <div class="card-body d-flex flex-column flex-grow-1">
                            <div class="mb-2 d-flex flex-wrap gap-2">
                                @foreach($post->categories as $category)
                                    <a href="{{ route('category.post.index', $category) }}" class="portal-chip">
                                        {{ $category->title }}
                                    </a>
                                @endforeach
                            </div>

                            <h3 class="h5 portal-post-title">
                                <a href="{{ route('post.show', $post) }}">{{ $post->title }}</a>
                            </h3>

                            <p class="portal-meta mb-2">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $post->displayDate()->translatedFormat('d. F Y') }}
                            </p>

                            <p class="mb-3">{{ $post->shortBody(22) }}</p>

                            <a href="{{ route('post.show', $post) }}" class="portal-inline-link mt-auto">
                                Weiterlesen <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </section>

    <div class="d-flex justify-content-center mt-4">
        {{ $posts->links() }}
    </div>
@else
    <div class="portal-card text-center py-5">
        <h2 class="h4 text-muted">Noch keine Artikel verfügbar</h2>
        <p class="text-muted mb-0">Bald finden Sie hier Ratgeber zu Umzug und Räumung in Wien.</p>
    </div>
@endif
