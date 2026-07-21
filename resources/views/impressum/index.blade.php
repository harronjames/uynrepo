@extends('layouts.portal')

@section('content')
    <section class="impressum-fullpage" aria-label="Impressum">
        @if($page->image)
            <img
                src="{{ route('impressum.image') }}"
                alt=""
                class="impressum-fullpage__image"
                loading="eager"
                decoding="async"
            >
        @else
            <div class="container-xl py-5 text-center">
                <h1 class="portal-post-title display-6 mb-3">{{ $page->title }}</h1>
                <p class="text-muted mb-0">Impressum folgt in Kürze.</p>
            </div>
        @endif
    </section>
@endsection
