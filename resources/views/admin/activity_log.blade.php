@extends("layout.main")
@section("title", "Logs")
@section("content")

<!-- Top Navbar -->
<div class="top-navbar">
    <img src="{{ asset('images/logoo.png') }}" alt="" class="logo-nav">
    <div class="user-greeting">Hello, Admin</div>
</div>

<!-- Sidebar + Content -->
<div class="main-wrapper">
    {{-- Include the sidebar --}}
  @include('layout.sidebar')
    <div class="main-content">
        <div class="container mt-5">
            <h3>Activity Logs</h3>
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
</div>
@endsection