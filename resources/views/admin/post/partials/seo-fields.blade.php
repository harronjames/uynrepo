<div class="card admin-seo-card mb-4">
    <div class="card-header">SEO &amp; Schema</div>
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label" for="meta_title">Meta Title</label>
            <input
                type="text"
                id="meta_title"
                class="form-control"
                name="meta_title"
                maxlength="70"
                data-counter="meta_title_count"
                value="{{ $metaTitle }}"
                placeholder="Leer lassen = Beitragstitel"
            >
            <div class="d-flex justify-content-between">
                <div class="form-text">Max. 70 Zeichen. Erscheint in Google als Titel.</div>
                <div class="form-text char-counter" id="meta_title_count">0 / 70</div>
            </div>
            @error('meta_title')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="meta_description">Meta Description</label>
            <textarea
                id="meta_description"
                class="form-control"
                name="meta_description"
                rows="3"
                maxlength="160"
                data-counter="meta_description_count"
                placeholder="155–160 Zeichen für die Suchergebnis-Beschreibung"
            >{{ $metaDescription }}</textarea>
            <div class="d-flex justify-content-between">
                <div class="form-text">Ideal 155–160 Zeichen. Wird in <code>&lt;meta name="description"&gt;</code> und Open Graph ausgegeben.</div>
                <div class="form-text char-counter" id="meta_description_count">0 / 160</div>
            </div>
            @error('meta_description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="meta_keywords">Meta Keywords</label>
            <input
                type="text"
                id="meta_keywords"
                class="form-control"
                name="meta_keywords"
                maxlength="255"
                value="{{ $metaKeywords }}"
                placeholder="Umzug Wien, Entrümpelung, Halteverbotszone"
            >
            @error('meta_keywords')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="form-label" for="schema_json">Custom Schema Markup (JSON-LD)</label>
            <textarea
                id="schema_json"
                class="form-control js-schema-json"
                name="schema_json"
                data-status="schema_json_status"
                spellcheck="false"
                placeholder="{{ json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => []], JSON_UNESCAPED_SLASHES) }}"
            >{{ $schemaJson }}</textarea>
            <div id="schema_json_status" class="form-text schema-status"></div>
            @error('schema_json')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
