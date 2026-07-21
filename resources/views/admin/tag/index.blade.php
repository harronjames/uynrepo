@extends('layouts.wrapper-admin', ['title' => 'Tags'])

@section('content')
    <h1 class="mb-3">Tags</h1>
    <a href="{{ route('admin.tag.create') }}" class="btn btn-primary mb-3">Add</a>

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
        @foreach($tags as $tag)
            <tr>
                <td>{{ $tag->id }}</td>
                <td>{{ $tag->title }}</td>
                <td><a href="{{ route('admin.tag.show', $tag->id) }}">View</a></td>
                <td><a href="{{ route('admin.tag.edit', $tag->id) }}" class="text-success">Edit</a></td>
                <td>
                    <form action="{{ route('admin.tag.delete', $tag->id) }}" method="post">
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
