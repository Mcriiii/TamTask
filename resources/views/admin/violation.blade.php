@extends("layout.main")
@section("title", "Violation Reports")
@section("content")
@php
    $prefix = Auth::user()->role == 'admin' ? 'admin.' : '';
    $minor = [
        'Not wearing prescribed uniform/attire',
        'Entry without ID',
        'Possession of pornographic materials in any form/medium',
        'Possession of any harmful gadget/weapon',
        'Possession of cigarette and e-cigarette on campus',
        'Possession of alcoholic drink',
        'Simple misconduct',
        'Smoking on campus',
        'Eating and drinking in restricted areas',
        'Public display of intimacy',
        'All other acts embodied in the classroom policy including those of full online classes policies',
        'All other acts/offenses of misconduct in any form'
    ];
    $major = [
        'Possession of prohibited drug',
        'Possession of explosive materials',
        'Possession of deadly weapon/firearms',
        'Acts of subversion, rebellion and inciting to sedition',
        'Possession of offensive/subversive materials',
        'Distribution of offensive/subversive materials in person or via electronic medium',
        'Being under the influence of liquor/prohibited drugs'
    ];
@endphp

<div class="top-navbar">
    <img src="{{ asset('images/logoo.png') }}" class="logo-nav" alt="">
    <div class="user-greeting">Hello, {{ Auth::user()->first_name }}</div>
</div>

<div class="main-wrapper">
    @include('layout.sidebar')

    <div class="main-content">
        <div class="container mt-5">
            <div class="card shadow rounded">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Violation Reports</h4>
                    <button class="btn btn-light text-danger" data-bs-toggle="modal" data-bs-target="#addViolationModal">
                        <i class="fas fa-plus"></i> Add Violation
                    </button>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route($prefix . 'violations.index') }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search name or offense">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_reported" class="form-control" value="{{ request('date_reported') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">-- Status --</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Complete" {{ request('status') == 'Complete' ? 'selected' : '' }}>Complete</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-danger">Filter</button>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Violation No</th>
                                    <th>Name</th>
                                    <th>Student No</th>
                                    <th>Offense</th>
                                    <th>Level</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($violations as $v)
                                <tr>
                                    <td>{{ $v->violation_no }}</td>
                                    <td>{{ $v->full_name }}</td>
                                    <td>{{ $v->student_no }}</td>
                                    <td>{{ $v->offense }}</td>
                                    <td>{{ $v->level }}</td>
                                    <td class="text-{{ $v->status == 'Complete' ? 'primary' : 'danger' }}">{{ $v->status }}</td>
                                    <td>{{ $v->date_reported }}</td>
                                    <td>
                                        <form method="POST" action="{{ route($prefix . 'violations.destroy', $v->id) }}" onsubmit="return confirm('Are you sure?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="8">No violations found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $violations->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addViolationModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route($prefix . 'violations.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Violation</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="violation_no" value="{{ 'VIO-' . strtoupper(uniqid()) }}">
                <div class="mb-2"><label>Full Name</label><input name="full_name" class="form-control" required></div>
                <div class="mb-2"><label>Student No</label><input name="student_no" class="form-control" required></div>
                <div class="mb-2"><label>Email</label><input type="email" name="student_email" class="form-control" required></div>
                <div class="mb-2"><label>Date</label><input type="date" name="date_reported" class="form-control" required></div>
                <div class="mb-2"><label>Year & Degree</label><input name="yearlvl_degree" class="form-control" required></div>
                <div class="mb-2"><label>Offense</label>
                    <select name="offense" class="form-select" required>
                        <optgroup label="Minor Offenses">
                            @foreach($minor as $offense)
                                <option>{{ $offense }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Major Offenses">
                            @foreach($major as $offense)
                                <option>{{ $offense }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="mb-2"><label>Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Pending">Pending</option>
                        <option value="Complete">Complete</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection
