@if (!empty($structuredData))
    <script type="application/ld+json">
        {!! \App\Support\StructuredData::toJson($structuredData) !!}
    </script>
@endif
