@extends('layouts.wrapper-admin', ['title' => 'Add post'])

@section('content')
    <h1>Add post</h1>

    <form method="post" action="{{ route('admin.post.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body pl-0">
            <div class="form-group w-50 mb-3">
                <label class="form-label" for="title">Post title</label>
                <input type="text" id="title" class="form-control" name="title"
                       placeholder="Post title" value="{{ old('title') }}" required>
                @error('title')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            @include('admin.post.partials.editor', ['content' => old('content')])

            @include('admin.post.partials.seo-fields', [
                'metaTitle' => old('meta_title'),
                'metaDescription' => old('meta_description'),
                'metaKeywords' => old('meta_keywords'),
                'schemaJson' => old('schema_json'),
            ])

            <div class="form-group w-50 mb-3">
                <label class="form-label">Cover image (detail page) <span class="text-muted">(optional, WebP only)</span></label>
                <input type="file" class="form-control" name="main_image" accept="{{ \App\Support\WebpImage::ACCEPT }}">
                <div class="form-text">Shown at the top of the article. Other formats are rejected.</div>
                @error('main_image')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group w-50 mb-3">
                <label class="form-label">Small card image <span class="text-muted">(optional, WebP only)</span></label>
                <input type="file" class="form-control" name="preview_image" accept="{{ \App\Support\WebpImage::ACCEPT }}">
                <div class="form-text">Used on the homepage and category lists. If empty, the cover image is shown instead.</div>
                @error('preview_image')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group w-25 mb-3">
                <label>Select category</label>
                <select class="form-control" name="category_id">
                    @foreach($categories as $category)
                        <option
                            value="{{ $category->id }}" {{ $category->id == old('category_id') ? ' selected' : '' }}>
                            {{ $category->title }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            @include('admin.post.partials.tags', [
                'tags' => $tags,
                'selectedTagIds' => old('tag_ids', []),
            ])
        </div>
        <input type="submit" class="btn btn-primary" value="Create post">
    </form>
@endsection
