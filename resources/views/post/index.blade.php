@extends('layouts.with-sidebar')

@section('page-content')
    <section class="portal-hero">
        <p class="text-uppercase small mb-2 opacity-75">Ratgeberportal für Wien</p>
        <h1 class="mb-3">Umzug, Räumung &amp; Leben in Wien – praxisnah erklärt</h1>
        <p class="mb-0 lead opacity-90">
            Checklisten, Kosten-Tipps und lokale Empfehlungen für Umzug, Entrümpelung und Wohnen in Österreich.
        </p>
    </section>

    @include('post.index.latest_posts')
    @include('post.index.liked_posts')
@endsection
