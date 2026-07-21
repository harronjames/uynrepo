@extends('layouts.portal')

@section('content')
    <div class="container-xl portal-shell py-4">
        <div class="row g-4">
            <div class="col-lg-8">
                @include('layouts.wrapper._breadcrumbs')

                @yield('page-content')
            </div>

            <aside class="col-lg-4" aria-label="Seitenleiste">
                @include('layouts.wrapper._sidebar')
            </aside>
        </div>
    </div>
@endsection
