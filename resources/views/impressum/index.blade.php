@extends('layouts.with-sidebar')

@section('page-content')
    <section class="portal-article-content text-center">
        <h1 class="portal-post-title display-6 mb-4">{{ $page->title }}</h1>

        @if($page->image)
            <figure class="mb-0">
                <img src="{{ route('impressum.image') }}" alt="" class="img-fluid rounded-4" loading="lazy">
            </figure>
        @else
            <p class="text-muted mb-0">Impressum folgt in Kürze.</p>
        @endif
    </section>
@endsection
