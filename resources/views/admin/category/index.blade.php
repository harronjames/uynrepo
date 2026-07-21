@extends('layouts.wrapper-admin', ['title' => 'Categories'])

@section('content')
    <h1 class="mb-3">Categories</h1>
    <a href="{{ route('admin.category.create') }}" class="btn btn-primary mb-3">Add</a>

    <table class="table table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>View</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        </thead>
        <tbody>
        @foreach($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>{{ $category->title }}</td>
                <td><a href="{{ route('admin.category.show', $category->id) }}">View</a></td>
                <td><a href="{{ route('admin.category.edit', $category->id) }}" class="text-success">Edit</a></td>
                <td>
                    <form action="{{ route('admin.category.delete', $category->id) }}" method="post">
                        @csrf
                        @method('delete')
                        <button type="submit" class="border-0 bg-transparent text-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
