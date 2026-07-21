@extends('layouts.wrapper-admin', ['title' => $post->title])

@section('content')
    <h1>Edit post</h1>

    <form method="post" action="{{ route('admin.post.update', $post->id) }}" enctype="multipart/form-data">
        @csrf
        @method('patch')
        <div class="card-body pl-0">
            <div class="form-group w-25 mb-3">
                <label class="form-label">Title</label>
                <input type="text" class="form-control" name="title"
                       value="{{ old('title', $post->title) }}">
                @error('title')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label class="form-label">Text</label>
                <textarea name="content" class="form-control" rows="6">{{ old('content', $post->content) }}</textarea>
                @error('content')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group w-50 mb-3">
                <label class="form-label">Preview image <span class="text-muted">(optional, WebP only)</span></label>
                @if($post->preview_image)
                    <div class="mb-2">
                        <img src="{{ $post->preview_image }}" alt="preview_image" class="img-fluid rounded border" style="max-width: 240px;">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="remove_preview_image" value="1" id="remove_preview_image">
                        <label class="form-check-label" for="remove_preview_image">Bild entfernen</label>
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
                        <label class="form-check-label" for="remove_main_image">Bild entfernen</label>
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
            <div class="col-12 col-sm-6 mb-3">
                <div class="form-group">
                    <label class="form-label">Select tags</label>
                    <div class="select2-purple">
                        <select class="select2" multiple="multiple" data-dropdown-css-class="select2-purple"
                                style="width: 100%;" name="tag_ids[]">
                            @foreach( $tags as $tag)
                                <option
                                    {{ in_array($tag->id, old('tag_ids', $post->tags->pluck('id')->all())) ? ' selected' : '' }} value="{{ $tag->id }}">{{ $tag->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @error('tag_ids')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <input type="submit" class="btn btn-primary mt-4 mb-5" value="Update post">
    </form>
@endsection
