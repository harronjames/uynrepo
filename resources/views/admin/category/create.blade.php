@extends('layouts.wrapper-admin', ['title' => 'Add category'])

@section('content')
    <h1 class="mb-3">Add category</h1>

    <form method="post" action="{{ route('admin.category.store') }}" class="w-50">
        @csrf
        <div class="mb-3">
            <label class="form-label">Category title</label>
            <input type="text" class="form-control" name="title" placeholder="Category title" value="{{ old('title') }}">
            @error('title')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
@endsection
