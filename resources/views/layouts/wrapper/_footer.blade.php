<footer class="portal-footer mt-5">
    <div class="container-xl py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <a class="portal-brand text-decoration-none d-inline-block mb-3" href="{{ route('main.index') }}">
                    <span class="portal-brand-title text-white">Umzugland.at</span>
                </a>
                <p class="text-secondary mb-0">
                    Ihr Ratgeber für Umzug, Räumung, Entrümpelung und praktisches Leben in Wien und Österreich.
                </p>
            </div>

            <div class="col-6 col-lg-2">
                <h2 class="h6 text-white text-uppercase mb-3">Navigation</h2>
                <ul class="list-unstyled portal-footer-links">
                    <li><a href="{{ route('main.index') }}">Startseite</a></li>
                    <li><a href="{{ route('category.index') }}">Themen</a></li>
                    <li><a href="{{ route('about.index') }}">Über uns</a></li>
                    <li><a href="{{ route('contact.index') }}">Kontakt</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-3">
                <h2 class="h6 text-white text-uppercase mb-3">Beliebte Themen</h2>
                <ul class="list-unstyled portal-footer-links">
                    @foreach (($sidebarCategories ?? collect())->take(6) as $footerCategory)
                        <li>
                            <a href="{{ route('category.post.index', $footerCategory) }}">
                                {{ $footerCategory->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="col-lg-3">
                <h2 class="h6 text-white text-uppercase mb-3">Servicegebiet</h2>
                <p class="text-secondary mb-2">Wien, Niederösterreich und ganz Österreich.</p>
                <a href="{{ route('contact.index') }}" class="portal-inline-link-light">Jetzt Kontakt aufnehmen</a>
            </div>
        </div>

        <div class="border-top border-secondary-subtle pt-4 mt-4 text-center text-secondary small">
            © {{ date('Y') }} Umzugland.at. Alle Rechte vorbehalten.
            <span class="mx-2">·</span>
            <a href="{{ route('impressum.index') }}" class="portal-inline-link-light">Impressum</a>
            <span class="mx-2">·</span>
            <a href="{{ route('seo.sitemap') }}" class="portal-inline-link-light">Sitemap</a>
        </div>
    </div>
</footer>
