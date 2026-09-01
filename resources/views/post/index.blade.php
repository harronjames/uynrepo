@extends('layouts.with-sidebar')

@section('page-content')
    <section class="portal-hero">
        <p class="text-uppercase small mb-2 opacity-75">Ratgeberportal für Wien</p>
        <h1 class="mb-3">Umzug, Räumung &amp; Leben in Wien – praxisnah erklärt</h1>
        <p class="mb-0 lead opacity-90">
            Unabhängige Checklisten, Kosten-Tipps und lokale Infos – rein zur Orientierung, ohne Verkauf oder Dienstleistungsangebot.
            Professionelle <a href="https://sofortentrumpelung.at/" class="text-white text-decoration-underline">Wohnungsentrümpelung Wien</a> unterstützt Sie bei der schnellen Entlastung vor dem Umzug.
        </p>
    </section>

    @include('post.index.latest_posts')
    @include('post.index.liked_posts')
@endsection
