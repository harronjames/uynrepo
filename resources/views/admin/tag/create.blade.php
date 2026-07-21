@extends('layouts.wrapper-admin', ['title' => 'Add tag'])

@section('content')
    <h1 class="mb-3">Add tag</h1>

    <form method="post" action="{{ route('admin.tag.store') }}" class="w-50">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tag title</label>
            <input type="text" class="form-control" name="title" placeholder="Tag title" value="{{ old('title') }}">
            @error('title')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
@endsection
