@extends('layouts.with-sidebar')

@section('page-content')
    <section class="portal-hero">
        <p class="text-uppercase small mb-2 opacity-75">Ratgeberportal für Wien</p>
        <h1 class="mb-3">Umzug, Räumung &amp; Leben in Wien – praxisnah erklärt</h1>
        <p class="mb-0 lead opacity-90">
            Stark vernachlässigte Wohnräume bergen oft hygienische Risiken und erfordern eine fachgerechte Sonderreinigung. Eine strukturierte
            <a href="https://sofortentrumpelung.at/leistungen/messie-entruempelung" class="text-white text-decoration-underline">Messie-Entrümpelung in Wien</a>
            sorgt für die umweltgerechte Müllentsorgung und stellt den hygienischen Grundzustand der Räume zuverlässig wieder her.
        </p>
    </section>

    @include('post.index.latest_posts')
    @include('post.index.liked_posts')
@endsection
