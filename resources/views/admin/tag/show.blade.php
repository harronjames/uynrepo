@extends('layouts.wrapper-admin', ['title' => 'Tag'])

@section('content')
    <h1 class="mb-3">{{ $tag->title }}</h1>

    <table class="table table-bordered w-50">
        <tr>
            <th>ID</th>
            <td>{{ $tag->id }}</td>
        </tr>
        <tr>
            <th>Title</th>
            <td>{{ $tag->title }}</td>
        </tr>
    </table>

    <a href="{{ route('admin.tag.edit', $tag->id) }}" class="btn btn-primary">Edit</a>
@endsection
