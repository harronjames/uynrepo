@php
    $org = config('seo.organization');
    $address = $org['address'];
@endphp

<ul class="list-unstyled mb-0">
    <li class="mb-2">{{ $org['name'] }}</li>
    <li class="mb-2">{{ $org['owner'] }}</li>
    <li class="mb-2">
        {{ $address['street'] }}, {{ $address['postal_code'] }} {{ $address['locality'] }}, Österreich
    </li>
    <li class="mb-2">
        Telefon:
        <a href="tel:{{ $org['telephone'] }}" class="portal-inline-link">{{ $org['telephone_display'] }}</a>
    </li>
    <li class="mb-0">
        E-Mail:
        <a href="mailto:{{ $org['email'] }}" class="portal-inline-link">{{ $org['email'] }}</a>
    </li>
</ul>
