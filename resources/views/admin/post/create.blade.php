@extends('layouts.wrapper-admin', ['title' => 'Add post'])

@section('content')
    <h1>Add post</h1>

    <form method="post" action="{{ route('admin.post.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body pl-0">
            <div class="form-group w-25 mb-3">
                <label>Post title</label>
                <input type="text" class="form-control" name="title"
                       placeholder="Post title" value="{{ old('title') }}">
                @error('title')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label>Text</label>
                <textarea class="form-control" name="content" rows="6">{{ old('content') }}</textarea>
                @error('content')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group w-50 mb-3">
                <label>Preview image <span class="text-muted">(optional, WebP only)</span></label>
                <input type="file" class="form-control" name="preview_image" accept="{{ \App\Support\WebpImage::ACCEPT }}">
                @error('preview_image')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group w-50 mb-3">
                <label>Main image <span class="text-muted">(optional, WebP only)</span></label>
                <input type="file" class="form-control" name="main_image" accept="{{ \App\Support\WebpImage::ACCEPT }}">
                @error('main_image')
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
            <div class="col-12 col-sm-6 mb-3">
                <div class="form-group">
                    <label>Select tags</label>
                    <div class="select2-purple">
                        <select class="select2" multiple="multiple" data-dropdown-css-class="select2-purple"
                                style="width: 100%;" name="tag_ids[]">
                            @foreach( $tags as $tag)
                                <option
                                    {{ is_array( old('tag_ids')) && in_array($tag->id, old('tag_ids')) ? ' selected' : '' }} value="{{ $tag->id }}">{{ $tag->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @error('tag_ids')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <input type="submit" class="btn btn-primary" value="Create post">
    </form>
@endsection
