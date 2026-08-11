@extends('layouts.with-sidebar')

@section('page-content')
    <section class="portal-article-content impressum-legal">
        <h1 class="portal-post-title display-6 mb-4">{{ $page->title }}</h1>

        <div class="mb-4">
            <h2 class="h5 mb-3">Medieninhaber</h2>
            <ul class="list-unstyled mb-0">
                <li class="mb-2"><strong>Medieninhaber:</strong> Mesut Duman</li>
                <li class="mb-2"><strong>Wohnort:</strong> Wien, Österreich</li>
                <li class="mb-0">
                    <strong>Kontakt:</strong>
                    <a href="mailto:info@umzugland.at" class="portal-inline-link">info@umzugland.at</a>
                </li>
            </ul>
        </div>

        @if($page->image)
            <figure class="mb-4 text-center">
                <img
                    src="{{ route('impressum.image') }}"
                    alt=""
                    class="img-fluid rounded-4 impressum-inline-image"
                    loading="lazy"
                    decoding="async"
                >
            </figure>
        @endif

        <h2 class="h4 mt-4 mb-3">Haftungsausschluss (Disclaimer)</h2>

        <h3 class="h5 mt-4 mb-2">Haftung für Inhalte</h3>
        <p>
            Die Inhalte unserer Seiten wurden mit größter Sorgfalt erstellt. Für die Richtigkeit,
            Vollständigkeit und Aktualität der Inhalte können wir jedoch keine Gewähr übernehmen.
            Als Diensteanbieter sind wir gemäß allgemeinen Gesetzen für eigene Inhalte auf diesen
            Seiten verantwortlich. Wir sind jedoch nicht verpflichtet, übermittelte oder gespeicherte
            fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine
            rechtswidrige Tätigkeit hinweisen.
        </p>

        <h3 class="h5 mt-4 mb-2">Haftung für Links</h3>
        <p>
            Unser Angebot enthält Links zu externen Webseiten Dritter, auf deren Inhalte wir keinen
            Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen.
            Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der
            Seiten verantwortlich. Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist
            jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden
            von Rechtsverletzungen werden wir derartige Links umgehend entfernen.
        </p>

        <h3 class="h5 mt-4 mb-2">Urheberrecht</h3>
        <p class="mb-0">
            Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen
            dem österreichischen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede
            Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen
            Zustimmung des jeweiligen Autors bzw. Erstellers. Downloads und Kopien dieser Seite sind
            nur für den privaten, nicht kommerziellen Gebrauch gestattet.
        </p>

        <hr class="my-4">

        <p class="text-muted small mb-0">
            Umzugland.at ist ein unabhängiges Informations- und Ratgeberportal. Wir bieten keine
            Verkaufs- oder Dienstleistungsangebote an und vermitteln keine Aufträge.
        </p>
    </section>
@endsection
