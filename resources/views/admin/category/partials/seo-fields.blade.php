<div class="mb-3">
    <label class="form-label" for="meta_title">Meta Title</label>
    <input type="text" id="meta_title" class="form-control" name="meta_title" maxlength="70"
           data-counter="meta_title_count" value="{{ $metaTitle }}">
    <div class="d-flex justify-content-between">
        <div class="form-text">Max. 70 Zeichen</div>
        <div class="form-text char-counter" id="meta_title_count">0 / 70</div>
    </div>
    @error('meta_title')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label class="form-label" for="meta_description">Meta Description</label>
    <textarea id="meta_description" class="form-control" name="meta_description" rows="3"
              data-counter="meta_description_count" data-recommended="160" data-warn="meta_description_warn">{{ $metaDescription }}</textarea>
    <div class="d-flex justify-content-between">
        <div class="form-text">Empfohlen: 155–160 Zeichen. Längerer Text wird gespeichert.</div>
        <div class="form-text char-counter" id="meta_description_count">0 / 160</div>
    </div>
    <div id="meta_description_warn" class="form-text text-warning d-none">
        Über 160 Zeichen: Google kürzt in den Suchergebnissen, der Text bleibt aber vollständig erhalten.
    </div>
    @error('meta_description')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label class="form-label" for="meta_keywords">Meta Keywords</label>
    <input type="text" id="meta_keywords" class="form-control" name="meta_keywords" maxlength="255"
           value="{{ $metaKeywords }}">
    @error('meta_keywords')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
