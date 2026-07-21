@extends('layouts.wrapper-admin', ['title' => 'Impressum'])

@section('content')
    <h1>Impressum</h1>
    <p class="text-muted">One optional WebP image only. This page is noindex (not indexed by search engines).</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="post" action="{{ route('admin.page.impressum.update') }}" enctype="multipart/form-data" class="mt-3">
        @csrf
        @method('patch')

        <div class="mb-3 w-50">
            <label class="form-label">Title</label>
            <input type="text" class="form-control" name="title" value="{{ old('title', $page->title) }}">
            @error('title')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 w-50">
            <label class="form-label">Image (optional, WebP only)</label>
            @if($page->image)
                <div class="mb-2">
                    <img src="{{ route('impressum.image') }}" alt="" class="img-fluid rounded border" style="max-width: 320px;">
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
                    <label class="form-check-label" for="remove_image">Remove image</label>
                </div>
            @endif
            <input type="file" class="form-control" name="image" accept="{{ \App\Support\WebpImage::ACCEPT }}">
            @error('image')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('impressum.index') }}" class="btn btn-outline-secondary" target="_blank">View page</a>
    </form>
@endsection
