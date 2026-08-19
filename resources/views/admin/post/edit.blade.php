@extends('layouts.wrapper-admin', ['title' => $post->title])

@section('content')
    <h1>Edit post</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="post" action="{{ route('admin.post.update', $post->id) }}" enctype="multipart/form-data">
        @csrf
        @method('patch')
        <div class="card-body pl-0">
            <div class="form-group w-50 mb-3">
                <label class="form-label" for="title">Title</label>
                <input type="text" id="title" class="form-control" name="title"
                       value="{{ old('title', $post->title) }}" required>
                @error('title')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            @include('admin.post.partials.editor', ['content' => old('content', $post->content)])

            @include('admin.post.partials.seo-fields', [
                'metaTitle' => old('meta_title', $post->meta_title),
                'metaDescription' => old('meta_description', $post->meta_description),
                'metaKeywords' => old('meta_keywords', $post->meta_keywords),
                'schemaJson' => old('schema_json', $post->schema_json),
            ])

            <div class="form-group w-50 mb-3">
                <label class="form-label">Preview image <span class="text-muted">(optional, WebP only)</span></label>
                @if($post->preview_image)
                    <div class="mb-2">
                        <img src="{{ $post->preview_image }}" alt="preview_image" class="img-fluid rounded border" style="max-width: 240px;">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="remove_preview_image" value="1" id="remove_preview_image">
                        <label class="form-check-label text-danger" for="remove_preview_image">
                            Remove preview image
                        </label>
                    </div>
                @endif
                <input type="file" class="form-control" name="preview_image" accept="{{ \App\Support\WebpImage::ACCEPT }}">
                @error('preview_image')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group w-50 mb-3">
                <label class="form-label">Main image <span class="text-muted">(optional, WebP only)</span></label>
                @if($post->main_image)
                    <div class="mb-2">
                        <img src="{{ $post->main_image }}" alt="main_image" class="img-fluid rounded border" style="max-width: 240px;">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="remove_main_image" value="1" id="remove_main_image">
                        <label class="form-check-label text-danger" for="remove_main_image">
                            Remove main image
                        </label>
                    </div>
                @endif
                <input type="file" class="form-control" name="main_image" accept="{{ \App\Support\WebpImage::ACCEPT }}">
                @error('main_image')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group w-25 mb-3">
                <label>Select category</label>
                @php $currentCategoryId = old('category_id', $post->categories->first()?->id); @endphp
                <select class="form-control" name="category_id">
                    @foreach($categories as $category)
                        <option
                            value="{{ $category->id }}" {{ (int) $currentCategoryId === (int) $category->id ? ' selected' : '' }}>
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
                'selectedTagIds' => old('tag_ids', $post->tags->pluck('id')->all()),
            ])
        </div>
        <input type="submit" class="btn btn-primary mt-4 mb-5" value="Update post">
    </form>
@endsection
