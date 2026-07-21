@extends('layouts.with-sidebar')

@section('page-content')
    <section>
        <h1 class="portal-post-title display-6 mb-4">Alle Themen</h1>
        <p class="text-muted mb-4">
            Entdecken Sie Ratgeber zu Umzug, Räumung, Entrümpelung und Leben in Wien – sortiert nach Themen.
        </p>

        <div class="row g-3">
            @forelse($categories as $category)
                <div class="col-md-6">
                    <article class="portal-card h-100">
                        <h2 class="h5 portal-post-title mb-2">
                            <a href="{{ route('category.post.index', $category) }}" class="text-decoration-none">
                                {{ $category->title }}
                            </a>
                        </h2>
                        <p class="text-muted small mb-3">
                            {{ $category->seoDescription() }}
                        </p>
                        <a href="{{ route('category.post.index', $category) }}" class="portal-inline-link">
                            Artikel in dieser Kategorie <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="portal-card text-center py-5 text-muted">
                        Noch keine Kategorien vorhanden.
                    </div>
                </div>
            @endforelse
        </div>
    </section>
@endsection
