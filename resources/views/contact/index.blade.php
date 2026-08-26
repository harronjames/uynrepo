@extends('layouts.with-sidebar')

@section('page-content')
    <section class="portal-article-content">
        <h1 class="portal-post-title display-6 mb-3">Kontakt</h1>
        <p class="text-muted mb-4">
            Sie haben Fragen zu unseren Ratgeberartikeln rund um Umzug, Entrümpelung oder das Leben
            in Wien? Schreiben Sie uns gerne – wir helfen bei inhaltlichen Rückfragen.
        </p>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="portal-card h-100">
                    <h2 class="h6 text-uppercase text-muted mb-3">E-Mail für Fragen</h2>
                    <a href="mailto:{{ config('seo.organization.email') }}" class="portal-inline-link">{{ config('seo.organization.email') }}</a>
                    <p class="text-muted small mt-3 mb-0">
                        Bitte beachten Sie: Umzugland.at ist ein Informationsportal und bietet keine
                        kostenpflichtigen Dienstleistungen oder Verkaufsangebote an.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="portal-card h-100">
                    <h2 class="h6 text-uppercase text-muted mb-3">Themenfokus</h2>
                    <p class="mb-0">Ratgeber zu Umzug, Räumung, Entrümpelung und Alltag in Wien sowie Österreich.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
