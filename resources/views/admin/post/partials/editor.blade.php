<div class="form-group mb-3">
    <label class="form-label" for="content">Text</label>
    <p class="small text-muted mb-2">
        Der Seitentitel ist bereits die H1. Im Text bevorzugt H2–H4 verwenden.
        Links: <code>rel="nofollow"</code>, <code>dofollow</code> (Standard) und <code>target="_blank"</code> über den SEO-Link-Button.
    </p>
    <textarea id="content" class="form-control js-wysiwyg" name="content" rows="12">{{ $content }}</textarea>
    @error('content')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
