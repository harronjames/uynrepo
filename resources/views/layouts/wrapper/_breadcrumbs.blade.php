@if (!empty($breadcrumbs))
    <nav class="portal-breadcrumbs mb-4" aria-label="Brotkrumen-Navigation">
        <ol class="breadcrumb mb-0">
            @foreach ($breadcrumbs as $crumb)
                <li class="breadcrumb-item {{ empty($crumb['url']) ? 'active' : '' }}" @if(empty($crumb['url'])) aria-current="page" @endif>
                    @if (!empty($crumb['url']))
                        <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                    @else
                        {{ $crumb['label'] }}
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
