@extends("layout.main")
@section("title", "Logs")
@section("content")


<style>
    .text-button {
        background: none;
        border: none;
        font-weight: bold;
        cursor: pointer;
        padding: 0;
        font-size: 0.9rem;
        text-decoration: underline;
    }

    .text-button:hover {
        opacity: 0.8;
        text-decoration: none;
    }

    .link-button {
        background: none;
        border: none;
        color: #006E44;
        /* FEU green */
        padding: 0;
        margin: 0;
        text-decoration: underline;
        cursor: pointer;
        font-weight: 500;
    }

    .link-button:hover {
        color: #004d3f;
        /* Slightly darker green */
        text-decoration: none;
    }
</style>
<div class="page-wrapper d-flex">
    @include('layout.sidebar')
    <div class="content-wrapper flex-grow-1 d-flex flex-column">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="mx-auto">
                <img src="{{ asset('images/name_logo.png') }}" alt="" class="logo-nav">
            </div>
            <div class="user-greeting">
                Hello, {{ Auth::user()->first_name }}
            </div>
        </div>

        <!-- Sidebar + Content -->
        <div class="main-wrapper">
            <div class="main-content">
                    <h3 class="mb-0 fw-bold fs-3">Activity Logs</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Email</th>
                                <th>Action</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->created_at }}</td>
                                <td>{{ $log->user->email ?? 'N/A' }}</td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->description }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $logs->links() }}
            </div>
        </div>
        @endsection