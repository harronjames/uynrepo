@extends('layouts.wrapper-admin', ['title' => $post->title])

@section('content')
    <h1>Post info</h1>

    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <tbody>
            <tr>
                <td>ID</td>
                <td>{{ $post->id }}</td>
            </tr>
            <tr>
                <td>Title</td>
                <td>{{ $post->title }}</td>
            </tr>
            <tr>
                <td>Categories</td>
                <td>TODO Show categories</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>{{ $post->statusLabel() }} ({{ $post->status }})</td>
            </tr>
            <tr>
                <td>Veröffentlichungsdatum</td>
                <td>{{ $post->published_at?->timezone(\App\Support\PublishQueue::timezone())->format('d.m.Y H:i') ?? '—' }}</td>
            </tr>
            <tr>
                <td>Warteschlangen-Position</td>
                <td>{{ $post->queue_position ?? '—' }}</td>
            </tr>
            <tr>
                <td>Erstellt am</td>
                <td>{{ $post->created_at }}</td>
            </tr>
            <tr>
                <td>Updated at</td>
                <td>{{ $post->updated_at }}</td>
            </tr>
            </tbody>
        </table>
    </div>

@endsection
