@extends('layouts.with-sidebar')

@section('page-content')
    <section class="portal-article-content impressum-legal">
        <h1 class="portal-post-title display-6 mb-4">Impressum / Offenlegung gemäß § 25 MedienG</h1>

        <h2 class="h5 mb-3">Medieninhaber und für den Inhalt verantwortlich</h2>
        @include('partials.organization-contact')

        @if($page->image)
            <figure class="mb-4 mt-4 text-center">
                <img
                    src="{{ route('impressum.image') }}"
                    alt=""
                    class="img-fluid rounded-4 impressum-inline-image"
                    loading="lazy"
                    decoding="async"
                >
            </figure>
        @endif

        <h2 class="h5 mt-4 mb-3">Grundlegende Richtung (Blattlinie)</h2>
        <p>
            Umzugland.at ist ein unabhängiges, redaktionelles Informations- und Ratgeberportal zu den
            Themen Umzug, Übersiedlung, Umzugsplanung, Kosten und damit zusammenhängenden Themen.
        </p>
        <p>
            Die Website dient ausschließlich der allgemeinen Information und Orientierung.
        </p>

        <h2 class="h5 mt-4 mb-3">Keine gewerblichen Umzugsleistungen</h2>
        <p>
            Über Umzugland.at werden keine eigenen Umzugs-, Transport- oder Entrümpelungsleistungen
            angeboten oder durchgeführt. Die Website nimmt keine Umzugsaufträge entgegen und vermittelt
            keine Verträge oder verbindlichen Angebote für Umzugsleistungen.
        </p>

        <h2 class="h5 mt-4 mb-3">Rechtliche Hinweise</h2>
        <p>
            Die auf dieser Website bereitgestellten Inhalte dienen ausschließlich der allgemeinen
            Information und Orientierung und stellen keine individuelle Rechts-, Steuer- oder sonstige
            Fachberatung dar.
        </p>
        <p>
            Informationen zu Preisen, Kosten, Abläufen, gesetzlichen Bestimmungen oder behördlichen
            Vorgaben werden mit größtmöglicher Sorgfalt erstellt. Dennoch kann keine Gewähr für deren
            Vollständigkeit, Richtigkeit und Aktualität übernommen werden.
        </p>
        <p>
            Eine Haftung für Schäden aufgrund der Nutzung der bereitgestellten Informationen ist, soweit
            gesetzlich zulässig, ausgeschlossen.
        </p>
        <p>
            Für Inhalte externer Websites, auf die mittels Links verwiesen wird, sind ausschließlich
            deren Betreiber verantwortlich. Bei Bekanntwerden rechtswidriger Inhalte werden entsprechende
            Links entfernt.
        </p>

        <h2 class="h5 mt-4 mb-3">Urheberrecht</h2>
        <p class="mb-0">
            Die auf Umzugland.at veröffentlichten Texte, Grafiken und sonstigen eigenen Inhalte sind
            urheberrechtlich geschützt. Eine Verwendung außerhalb der gesetzlich zulässigen Grenzen
            bedarf der vorherigen Zustimmung des jeweiligen Rechteinhabers.
        </p>
    </section>
@endsection
