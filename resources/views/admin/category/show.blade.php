@extends('layouts.wrapper-admin', ['title' => 'Category'])

@section('content')
    <h1 class="mb-3">{{ $category->title }}</h1>

    <table class="table table-bordered w-50">
        <tr>
            <th>ID</th>
            <td>{{ $category->id }}</td>
        </tr>
        <tr>
            <th>Title</th>
            <td>{{ $category->title }}</td>
        </tr>
        <tr>
            <th>Slug</th>
            <td>{{ $category->slug }}</td>
        </tr>
    </table>

    <a href="{{ route('admin.category.edit', $category->id) }}" class="btn btn-primary">Edit</a>
@endsection
