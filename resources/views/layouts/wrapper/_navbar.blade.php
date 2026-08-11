<div class="container-xl">
    <header class="portal-header border-bottom">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 py-4">
            <div>
                <a class="portal-brand text-decoration-none" href="{{ route('main.index') }}">
                    <span class="portal-brand-kicker">Unabhängiger Ratgeber</span>
                    <span class="portal-brand-title">Umzugland.at</span>
                </a>
                <p class="portal-brand-subtitle mb-0">Umzug, Räumung, Entrümpelung &amp; Leben in Wien</p>
            </div>

            <div class="portal-header-cta text-md-end">
                <a href="mailto:info@umzugland.at" class="btn btn-primary rounded-pill px-4">
                    Fragen an info@umzugland.at
                </a>
            </div>
        </div>

        <nav class="portal-nav nav border-top pt-3 pb-3" aria-label="Hauptnavigation">
            <a class="nav-link {{ request()->routeIs('main.index') ? 'active' : '' }}" href="{{ route('main.index') }}">Startseite</a>
            <a class="nav-link {{ request()->routeIs('category.*') ? 'active' : '' }}" href="{{ route('category.index') }}">Themen</a>
            <a class="nav-link {{ request()->routeIs('about.index') ? 'active' : '' }}" href="{{ route('about.index') }}">Über uns</a>
            <a class="nav-link {{ request()->routeIs('contact.index') ? 'active' : '' }}" href="{{ route('contact.index') }}">Kontakt</a>
        </nav>
    </header>
</div>
