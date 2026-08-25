@extends('layouts.wrapper-admin', ['title' => 'Posts'])

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h2 class="mb-0">Posts</h2>
        <a href="{{ route('admin.post.create') }}" class="btn btn-primary">Neuer Beitrag</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <ul class="nav nav-tabs mb-4">
        @foreach([
            'all' => 'Alle',
            'published' => 'Veröffentlicht',
            'scheduled' => 'Geplant / Warteschlange',
            'draft' => 'Entwurf',
        ] as $tab => $label)
            <li class="nav-item">
                <a
                    class="nav-link {{ $status === $tab ? 'active' : '' }}"
                    href="{{ route('admin.post.index', ['status' => $tab]) }}"
                >
                    {{ $label }}
                    <span class="badge bg-secondary ms-1">{{ $counts[$tab] ?? 0 }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    @if($queuePosts->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Veröffentlichungs-Warteschlange</strong>
                <span class="text-muted small">{{ $queuePosts->count() }} Beiträge warten</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Titel</th>
                            <th>Veröffentlichungsdatum</th>
                            <th>Verbleibende Zeit</th>
                            <th class="text-end">Aktionen</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($queuePosts as $queuePost)
                            <tr>
                                <td>{{ $queuePost->queue_position ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('admin.post.edit', $queuePost) }}">{{ $queuePost->title }}</a>
                                </td>
                                <td>
                                    {{ $queuePost->published_at?->timezone(\App\Support\PublishQueue::timezone())->format('d.m.Y H:i') }}
                                </td>
                                <td>{{ $queuePost->publishCountdown() }}</td>
                                <td class="text-end">
                                    <form action="{{ route('admin.post.publish-now', $queuePost) }}" method="post" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Jetzt veröffentlichen?')">
                                            Jetzt veröffentlichen
                                        </button>
                                    </form>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#reschedule-{{ $queuePost->id }}"
                                    >
                                        Datum ändern
                                    </button>
                                </td>
                            </tr>
                            <tr class="collapse" id="reschedule-{{ $queuePost->id }}">
                                <td colspan="5" class="bg-light">
                                    <form action="{{ route('admin.post.reschedule', $queuePost) }}" method="post" class="row g-2 align-items-end p-2">
                                        @csrf
                                        @method('patch')
                                        <div class="col-md-4">
                                            <label class="form-label small mb-0">Neues Datum</label>
                                            <input
                                                type="datetime-local"
                                                name="published_at"
                                                class="form-control form-control-sm"
                                                value="{{ $queuePost->published_at?->timezone(\App\Support\PublishQueue::timezone())->format('Y-m-d\TH:i') }}"
                                                required
                                            >
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small mb-0">Status</label>
                                            <select name="status" class="form-select form-select-sm">
                                                <option value="scheduled">Geplant</option>
                                                <option value="published">Veröffentlicht</option>
                                                <option value="draft">Entwurf</option>
                                            </select>
                                        </div>
                                        <div class="col-md-auto">
                                            <button type="submit" class="btn btn-sm btn-primary">Speichern</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Titel</th>
                <th>Status</th>
                <th>Veröffentlichung</th>
                <th class="text-end">Aktionen</th>
            </tr>
            </thead>
            <tbody>
            @forelse($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>{{ $post->title }}</td>
                    <td>
                        @php
                            $badge = match ($post->status) {
                                \App\Models\Post::STATUS_PUBLISHED => 'success',
                                \App\Models\Post::STATUS_SCHEDULED => 'warning text-dark',
                                default => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $badge }}">{{ $post->statusLabel() }}</span>
                    </td>
                    <td>
                        @if($post->published_at)
                            {{ $post->published_at->timezone(\App\Support\PublishQueue::timezone())->format('d.m.Y H:i') }}
                            @if($post->status === \App\Models\Post::STATUS_SCHEDULED)
                                <div class="small text-muted">{{ $post->publishCountdown() }}</div>
                            @endif
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('post.show', $post) }}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">Ansehen</a>
                        <a href="{{ route('admin.post.edit', $post) }}" class="btn btn-sm btn-outline-success">Bearbeiten</a>
                        @if($post->status === \App\Models\Post::STATUS_SCHEDULED)
                            <form action="{{ route('admin.post.publish-now', $post) }}" method="post" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Jetzt veröffentlichen</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.post.delete', $post) }}" method="post" class="d-inline">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Wirklich löschen?')">
                                Löschen
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Keine Beiträge in diesem Filter.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
