@extends('layouts.wrapper-admin', ['title' => 'Edit category'])

@section('content')
    <h1 class="mb-3">Edit category</h1>

    <form method="post" action="{{ route('admin.category.update', $category->id) }}" class="w-50">
        @csrf
        @method('patch')
        <div class="mb-3">
            <label class="form-label">Category title</label>
            <input type="text" class="form-control" name="title" value="{{ old('title', $category->title) }}">
            @error('title')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@endsection
