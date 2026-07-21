@extends('layouts.with-sidebar')

@section('page-content')
    <section>
        <h1 class="portal-post-title display-6 mb-2">{{ $category->title }}</h1>
        <p class="text-muted mb-4">{{ $category->seoDescription() }}</p>

        <div class="row g-4">
            @forelse($posts as $post)
                <div class="col-md-6">
                    <article class="portal-post-card h-100 d-flex flex-column">
                        @if($post->preview_image)
                            <a href="{{ route('post.show', $post) }}">
                                <img src="{{ $post->preview_image }}" alt="{{ $post->title }}" loading="lazy">
                            </a>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h2 class="h5 portal-post-title">
                                <a href="{{ route('post.show', $post) }}">{{ $post->title }}</a>
                            </h2>
                            <p class="portal-meta mb-2">{{ $post->created_at->translatedFormat('d. F Y') }}</p>
                            <p class="mb-3">{{ $post->shortBody(20) }}</p>
                            <a href="{{ route('post.show', $post) }}" class="portal-inline-link mt-auto">
                                Zum Artikel <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="portal-card text-center py-5 text-muted">
                        In dieser Kategorie sind noch keine Artikel veröffentlicht.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $posts->links() }}
        </div>
    </section>
@endsection
