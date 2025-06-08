@extends('layout.main')
@section('title', 'Lost and Found Dashboard')

@section('content')
    @php
        $prefix = Auth::user()->role == 'admin' ? 'admin.' : '';
    @endphp
    <div class="top-navbar">
        <img src="{{ asset('images/logoo.png') }}" alt="" class="logo-nav">
        <div class="user-greeting">
            @if(Auth::check())
                Hello, {{ Auth::user()->first_name }}
            @else
                Hello, Guest
            @endif
        </div>
    </div>

    <div class="main-wrapper">
        @include('layout.sidebar')
        <div class="main-content">
            <div class="container py-4">
                <h2 class="mb-4">📊 Lost & Found Analytics</h2>

                <form method="GET" class="mb-3">
                    <label>Select Month:</label>
                    <input type="month" name="month" value="{{ $month }}" class="form-control w-25 d-inline">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route($prefix . 'pdf.export') }}" class="btn btn-danger btn-sm float-end">Export to PDF</a>
                </form>

                <div class="alert alert-warning text-center">
                    <strong>⚠️ Most Frequently Lost Item:</strong>
                    <span class="h4 text-danger">{{ $topItem->item_type ?? 'N/A' }}</span>
                    ({{ $topItem->total ?? 0 }} times)
                </div>

                <canvas id="lostFoundChart" height="100"></canvas>

                <h5 class="mt-5">📝 Recent Lost & Found Reports</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Reporter</th>
                            <th>Item</th>
                            <th>Status</th>
                            <th>Date Reported</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent as $entry)
                            <tr>
                                <td>{{ $entry->ticket_no }}</td>
                                <td>{{ $entry->reporter_name }}</td>
                                <td>{{ $entry->item_type }}</td>
                                <td>
                                    <span class="badge bg-{{ $entry->status == 'Returned' ? 'success' : 'warning' }}">
                                        {{ $entry->status }}
                                    </span>
                                </td>
                                <td>{{ $entry->date_reported }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const labelsData = @json($labels);
        const countsData = @json($counts);
    </script>
    <script src="{{ asset('js/lostfound.js') }}"></script>
@endpush