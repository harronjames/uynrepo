@extends('layouts.with-sidebar')

@section('page-content')
    <article class="portal-article-content" itemscope itemtype="https://schema.org/BlogPosting">
        <header class="mb-4">
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach($post->categories as $category)
                    <a href="{{ route('category.post.index', $category) }}" class="portal-chip">
                        {{ $category->title }}
                    </a>
                @endforeach
            </div>

            <h1 class="portal-post-title display-6 mb-3" itemprop="headline">{{ $post->title }}</h1>

            <div class="portal-meta d-flex flex-wrap gap-3">
                <span><i class="bi bi-calendar3 me-1"></i>{{ $date->translatedFormat('d. F Y') }}</span>
                <span><i class="bi bi-clock me-1"></i>{{ $date->format('H:i') }} Uhr</span>
            </div>
        </header>

        @if($post->main_image)
            <figure class="mb-4">
                <img src="{{ $post->main_image }}" alt="{{ $post->title }}" class="img-fluid rounded-4 w-100" itemprop="image">
            </figure>
        @endif

        <div class="article-body" itemprop="articleBody">
            {!! $post->linkedContent() !!}
        </div>

        @if($post->categories->isNotEmpty())
            <nav class="mt-4 pt-4 border-top" aria-label="Themen-Übersicht">
                <h2 class="h6 text-uppercase text-muted mb-3">Weiterführende Themen</h2>
                <ul class="list-unstyled mb-0">
                    @foreach($post->categories as $category)
                        <li class="mb-2">
                            <a href="{{ route('category.post.index', $category) }}" class="portal-inline-link">
                                Alle Ratgeber zu {{ $category->title }} <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </li>
                    @endforeach
                    <li>
                        <a href="{{ route('category.index') }}" class="portal-inline-link">
                            Alle Themen auf Umzugland.at <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        @endif

        @if($tags->isNotEmpty())
            <footer class="mt-4 pt-4 border-top">
                <h2 class="h6 text-uppercase text-muted mb-3">Schlagwörter</h2>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                        <span class="portal-chip">{{ $tag->title }}</span>
                    @endforeach
                </div>
            </footer>
        @endif
    </article>

    @if($relatedPosts->isNotEmpty())
        <section class="mt-5 portal-related-grid" aria-label="Verwandte Artikel">
            <h2 class="h4 portal-post-title mb-3">Das könnte Sie auch interessieren</h2>
            <div class="row g-4">
                @foreach($relatedPosts as $relatedPost)
                    <div class="col-md-4">
                        <article class="portal-post-card h-100">
                            @if($relatedPost->card_image)
                                <a href="{{ route('post.show', $relatedPost) }}">
                                    <img src="{{ $relatedPost->card_image }}" alt="{{ $relatedPost->title }}" loading="lazy">
                                </a>
                            @endif
                            <div class="card-body">
                                <h3 class="h6 portal-post-title">
                                    <a href="{{ route('post.show', $relatedPost) }}">{{ $relatedPost->title }}</a>
                                </h3>
                                <p class="small text-muted mb-0">{{ $relatedPost->shortBody(16) }}</p>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
