@if (!empty($structuredData))
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson($structuredData) !!}</script>
@endif
@if (!empty($customJsonLd))
    <script type="application/ld+json">{!! $customJsonLd !!}</script>
@endif
