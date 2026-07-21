<div class="portal-sidebar sticky-lg-top">
    <section class="portal-card mb-4">
        <h2 class="portal-card-title h6 text-uppercase">Themen</h2>
        <ul class="portal-topic-list list-unstyled mb-0">
            @forelse ($sidebarCategories as $sidebarCategory)
                <li>
                    <a href="{{ route('category.post.index', $sidebarCategory) }}" class="portal-topic-link">
                        <span>{{ $sidebarCategory->title }}</span>
                        <span class="badge rounded-pill text-bg-light">{{ $sidebarCategory->posts_count }}</span>
                    </a>
                </li>
            @empty
                <li class="text-muted small">Noch keine Themen verfügbar.</li>
            @endforelse
        </ul>
        <a href="{{ route('category.index') }}" class="portal-inline-link mt-3 d-inline-flex align-items-center gap-1">
            Alle Themen ansehen <i class="bi bi-arrow-right-short"></i>
        </a>
    </section>

    <section class="portal-card mb-4">
        <h2 class="portal-card-title h6 text-uppercase">Beliebte Ratgeber</h2>
        <ul class="portal-link-list list-unstyled mb-0">
            @foreach ($sidebarPopularPosts as $sidebarPost)
                <li>
                    <a href="{{ route('post.show', $sidebarPost) }}" class="portal-link-item">
                        {{ $sidebarPost->title }}
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    <section class="portal-card mb-4">
        <h2 class="portal-card-title h6 text-uppercase">Neueste Artikel</h2>
        <ul class="portal-link-list list-unstyled mb-0">
            @foreach ($sidebarRecentPosts as $sidebarPost)
                <li>
                    <a href="{{ route('post.show', $sidebarPost) }}" class="portal-link-item">
                        {{ $sidebarPost->title }}
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    <section class="portal-card portal-card-accent">
        <h2 class="portal-card-title h6 text-uppercase">Schnellzugriff</h2>
        <ul class="portal-link-list list-unstyled mb-0">
            <li><a href="{{ route('main.index') }}" class="portal-link-item">Startseite</a></li>
            <li><a href="{{ route('category.index') }}" class="portal-link-item">Alle Kategorien</a></li>
            <li><a href="{{ route('about.index') }}" class="portal-link-item">Über uns</a></li>
            <li><a href="{{ route('contact.index') }}" class="portal-link-item">Kontakt</a></li>
        </ul>
    </section>
</div>
