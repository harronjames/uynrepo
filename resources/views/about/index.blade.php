@extends('layouts.with-sidebar')

@section('page-content')
    <section class="portal-article-content">
        <h1 class="portal-post-title display-6 mb-3">{{ $page->title }}</h1>

        <div class="article-body">
            {!! $page->content !!}
        </div>
    </section>
@endsection
