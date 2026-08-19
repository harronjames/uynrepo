@php
    $selectedTagIds = collect($selectedTagIds ?? [])->map(fn ($id) => (int) $id)->all();
@endphp

<div class="mb-3">
    <label class="form-label">Tags</label>
    <p class="small text-muted mb-2">Zum An- und Abwählen einfach klicken. Mehrfachauswahl möglich.</p>
    <div class="admin-tag-picker">
        @forelse($tags as $tag)
            <label class="admin-tag-chip">
                <input
                    type="checkbox"
                    name="tag_ids[]"
                    value="{{ $tag->id }}"
                    {{ in_array((int) $tag->id, $selectedTagIds, true) ? 'checked' : '' }}
                >
                <span>{{ $tag->title }}</span>
            </label>
        @empty
            <div class="text-muted small">Keine Tags vorhanden.</div>
        @endforelse
    </div>
    @error('tag_ids')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
