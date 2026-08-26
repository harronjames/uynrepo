@extends('layouts.with-sidebar')

@section('page-content')
    <section class="portal-article-content impressum-legal">
        <h1 class="portal-post-title display-6 mb-4">Datenschutzerklärung</h1>

        <p class="lead">
            Der Schutz Ihrer personenbezogenen Daten ist uns wichtig. Diese Datenschutzerklärung informiert
            Sie darüber, welche Daten bei einem Besuch von umzugland.at verarbeitet werden, zu welchem
            Zweck dies geschieht und welche Rechte Ihnen dabei zustehen.
        </p>

        <h2 class="h5 mt-4 mb-3">1. Verantwortlicher</h2>
        <p>Verantwortlicher im Sinne der Datenschutz-Grundverordnung (DSGVO) ist:</p>
        @include('partials.organization-contact')

        <h2 class="h5 mt-4 mb-3">2. Allgemeines zur Datenverarbeitung</h2>
        <p>
            Umzugland.at ist ein Informations- und Dienstleistungsportal. Personenbezogene Daten werden
            nur verarbeitet, soweit dies für den Betrieb der Website technisch notwendig ist oder Sie uns
            Daten freiwillig zur Verfügung stellen (z.&nbsp;B. per E-Mail-Anfrage für Umzugsdienstleistungen).
        </p>

        <h2 class="h5 mt-4 mb-3">3. Hosting und Server-Logfiles</h2>
        <p>Diese Website wird bei IONOS gehostet. Beim Aufruf der Website werden durch den Hosting-Anbieter automatisch sogenannte Server-Logfiles erfasst, die Ihr Browser automatisch übermittelt. Dazu gehören:</p>
        <ul>
            <li>IP-Adresse</li>
            <li>Datum und Uhrzeit der Anfrage</li>
            <li>aufgerufene Seite/Datei</li>
            <li>verwendeter Browser und Betriebssystem</li>
            <li>verweisende URL (Referrer)</li>
        </ul>
        <p>
            Diese Daten werden ausschließlich zur Gewährleistung eines störungsfreien Betriebs, zur
            Sicherheit der Website sowie zur technischen Fehleranalyse verarbeitet. Rechtsgrundlage ist
            Art.&nbsp;6 Abs.&nbsp;1 lit.&nbsp;f DSGVO (berechtigtes Interesse an einem stabilen und sicheren
            Betrieb). Eine Zusammenführung mit anderen Datenquellen erfolgt nicht.
        </p>

        <h2 class="h5 mt-4 mb-3">4. Content Delivery Network (Cloudflare)</h2>
        <p>
            Diese Website nutzt Dienste des Anbieters Cloudflare, Inc., 101 Townsend St, San Francisco,
            CA 94107, USA, unter anderem zum Schutz vor Spam-Bots (z.&nbsp;B. E-Mail-Adressen-Verschleierung)
            und zur Auslieferung von Website-Inhalten. Dabei können technische Daten wie Ihre IP-Adresse an
            Cloudflare übermittelt werden. Cloudflare kann auch außerhalb der EU verarbeiten; für
            Datenübermittlungen in die USA stützt sich Cloudflare auf Standardvertragsklauseln gemäß
            Art.&nbsp;46 DSGVO bzw. das EU-US Data Privacy Framework. Rechtsgrundlage ist Art.&nbsp;6 Abs.&nbsp;1
            lit.&nbsp;f DSGVO (berechtigtes Interesse an Sicherheit und zuverlässiger Bereitstellung der Website).
        </p>
        <p>
            Weitere Informationen finden Sie in der Datenschutzerklärung von Cloudflare:
            <a href="https://www.cloudflare.com/privacypolicy/" class="portal-inline-link" rel="noopener noreferrer" target="_blank">https://www.cloudflare.com/privacypolicy/</a>
        </p>

        <h2 class="h5 mt-4 mb-3">5. Cookies</h2>
        <p>
            Diese Website setzt keine zustimmungspflichtigen Tracking- oder Analyse-Cookies ein. Eine
            Einwilligung über ein Cookie-Banner ist daher nicht erforderlich.
        </p>

        <h2 class="h5 mt-4 mb-3">6. Kontaktaufnahme per E-Mail</h2>
        <p>
            Auf der Website ist eine E-Mail-Adresse zur Kontaktaufnahme angegeben. Wenn Sie uns per E-Mail
            kontaktieren, werden die von Ihnen mitgeteilten Daten (z.&nbsp;B. Absenderadresse, Name,
            Telefonnummer, Nachrichteninhalt bzw. Umzugsdetails) ausschließlich zur Bearbeitung und
            Beantwortung Ihrer Anfrage verarbeitet. Rechtsgrundlage ist Art.&nbsp;6 Abs.&nbsp;1 lit.&nbsp;b DSGVO
            (Vertragserfüllung oder vorvertragliche Maßnahmen) bzw. Art.&nbsp;6 Abs.&nbsp;1 lit.&nbsp;f DSGVO
            (berechtigtes Interesse an der Beantwortung von Anfragen). Die Daten werden gelöscht, sobald sie
            für die Bearbeitung nicht mehr erforderlich sind, sofern keine gesetzlichen Aufbewahrungspflichten
            entgegenstehen.
        </p>

        <h2 class="h5 mt-4 mb-3">7. Empfänger / Auftragsverarbeiter</h2>
        <p>
            Eine Übermittlung Ihrer Daten an Dritte erfolgt nur, soweit dies zur Erbringung der oben
            genannten Dienste notwendig ist (z.&nbsp;B. Hosting-Anbieter, Cloudflare). Mit allen
            Auftragsverarbeitern bestehen bzw. werden Verträge gemäß Art.&nbsp;28 DSGVO abgeschlossen. Eine
            Übermittlung zu Werbezwecken oder ein Verkauf Ihrer Daten an Dritte findet nicht statt.
        </p>

        <h2 class="h5 mt-4 mb-3">8. Speicherdauer</h2>
        <p>
            Personenbezogene Daten werden nur so lange gespeichert, wie dies für den jeweiligen Zweck
            erforderlich ist bzw. wie es gesetzliche Aufbewahrungsfristen vorsehen. Server-Logfiles werden
            in der Regel nach spätestens 14 Tagen automatisch gelöscht oder anonymisiert.
        </p>

        <h2 class="h5 mt-4 mb-3">9. Ihre Rechte</h2>
        <p>Ihnen stehen nach der DSGVO folgende Rechte zu:</p>
        <ul>
            <li>Recht auf Auskunft (Art.&nbsp;15 DSGVO)</li>
            <li>Recht auf Berichtigung (Art.&nbsp;16 DSGVO)</li>
            <li>Recht auf Löschung (Art.&nbsp;17 DSGVO)</li>
            <li>Recht auf Einschränkung der Verarbeitung (Art.&nbsp;18 DSGVO)</li>
            <li>Recht auf Datenübertragbarkeit (Art.&nbsp;20 DSGVO)</li>
            <li>Recht auf Widerspruch gegen die Verarbeitung (Art.&nbsp;21 DSGVO)</li>
            <li>Recht auf Widerruf einer erteilten Einwilligung mit Wirkung für die Zukunft (Art.&nbsp;7 Abs.&nbsp;3 DSGVO)</li>
        </ul>
        <p>
            Zur Ausübung dieser Rechte genügt eine formlose Mitteilung per E-Mail an
            <a href="mailto:{{ config('seo.organization.email') }}" class="portal-inline-link">{{ config('seo.organization.email') }}</a>.
        </p>

        <h2 class="h5 mt-4 mb-3">10. Beschwerderecht</h2>
        <p>
            Sie haben das Recht, sich bei der zuständigen Aufsichtsbehörde zu beschweren, wenn Sie der
            Ansicht sind, dass die Verarbeitung Ihrer personenbezogenen Daten gegen die DSGVO verstößt:
        </p>
        <p class="mb-1"><strong>Österreichische Datenschutzbehörde</strong></p>
        <p class="mb-1">Barichgasse 40–42, 1030 Wien</p>
        <p class="mb-1">Telefon: +43 1 52 152-0</p>
        <p class="mb-1">
            E-Mail:
            <a href="mailto:dsb@dsb.gv.at" class="portal-inline-link">dsb@dsb.gv.at</a>
        </p>
        <p class="mb-0">
            Web:
            <a href="https://www.dsb.gv.at" class="portal-inline-link" rel="noopener noreferrer" target="_blank">www.dsb.gv.at</a>
        </p>

        <h2 class="h5 mt-4 mb-3">11. Änderungen dieser Datenschutzerklärung</h2>
        <p class="mb-0">
            Wir behalten uns vor, diese Datenschutzerklärung anzupassen, um sie an geänderte Rechtslagen
            oder bei Änderungen der Website bzw. der eingesetzten Dienste anzupassen.
        </p>
    </section>
@endsection
