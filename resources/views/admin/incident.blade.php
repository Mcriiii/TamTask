@extends("layout.main") 
@section("title", "Incident Reports")
@section("content")
@php
    $prefix = Auth::user()->role == 'admin' ? 'admin.' : '';
@endphp
<!-- Top Navbar -->
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

<!-- Sidebar + Content -->
<div class="main-wrapper">
    @include('layout.sidebar')

    <div class="main-content">
        <div class="container mt-5">
            <div class="card shadow rounded">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Incident Reports</h4>
                    <button type="button" class="btn btn-light text-danger" data-bs-toggle="modal" data-bs-target="#addIncidentModal">
                        <i class="fas fa-plus"></i> Add Incident
                    </button>
                </div>
                <div class="card-body">
                    {{-- Filter --}}
                    <form method="GET" action="{{ route($prefix .$prefix .'incidents.index') }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name, ticket, or incident">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_reported" class="form-control" value="{{ request('date_reported') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="reporter_role" class="form-select">
                                <option value="">-- Role --</option>
                                <option value="Student" {{ request('reporter_role') == 'Student' ? 'selected' : '' }}>Student</option>
                                <option value="Teacher" {{ request('reporter_role') == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-danger me-1">Filter</button>
                                <a href="{{ route($prefix . 'incidents.index') }}" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </form>

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    {{-- Incident Table --}}
                    @if($incidents->isEmpty())
                        <p>No incident reports found.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ticket No</th>
                                    <th>Incident</th>
                                    <th>Reporter</th>
                                    <th>Date</th>
                                    <th>Level</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incidents as $incident)
                                <tr>
                                    <td>{{ $incident->ticket_no }}</td>
                                    <td>{{ $incident->incident }}</td>
                                    <td>{{ $incident->reporter_name }}</td>
                                    <td>{{ $incident->date_reported }}</td>
                                    <td>
                                        <span style="color: {{ $incident->level_color }}; font-weight: bold;">■</span> 
                                        {{ $incident->level }}
                                    </td>
                                    <td>{{ $incident->reporter_role }}</td>
                                    <td style="color: {{ $incident->status === 'Complete' ? 'blue' : 'red' }}">
                                        {{ $incident->status }}
                                    </td>
                                    <td>
                                        <form action="{{ route($prefix .'incidents.destroy', $incident->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this report?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $incidents->withQueryString()->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Incident Modal -->
<div class="modal fade" id="addIncidentModal" tabindex="-1" aria-labelledby="addIncidentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route($prefix .'incidents.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="addIncidentModalLabel">Add Incident Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Ticket No.</label>
                    <input type="text" name="ticket_no" class="form-control" value="{{ 'INC-' . strtoupper(uniqid()) }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Incident Description</label>
                    <textarea name="incident" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Reporter Name</label>
                    <input type="text" name="reporter_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date Reported</label>
                    <input type="date" name="date_reported" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Reporter Role</label>
                    <select name="reporter_role" class="form-select" required>
                        <option value="Student">Student</option>
                        <option value="Associates">Associates</option>
                        <option value="Security">Security</option>
                        <option value="SFU">SFU</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Pending">Pending</option>
                        <option value="Complete">Complete</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection
