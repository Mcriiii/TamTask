@extends("layout.main")
@section("title", "Violation Reports")
@section("content")

@php
    $prefix = Auth::user()->role === 'admin' ? 'admin.' : '';
    $minorOffenses = ['No ID', 'No Uniform', 'Possession: Cigarette', 'Simple Misconduct'];
    $majorOffenses = ['Prohibited Drugs', 'Deadly Weapon', 'Bullying', 'Harassment'];
@endphp

<!-- Top Navbar -->
<div class="top-navbar">
    <img src="{{ asset('images/logoo.png') }}" alt="" class="logo-nav">
    <div class="user-greeting">Hello, {{ Auth::user()->first_name ?? 'User' }}</div>
</div>

<div class="main-wrapper">
    @include('layout.sidebar')
    <div class="main-content">
        <div class="container mt-5">
            <div class="card shadow rounded">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Violation Reports</h4>
                    <button class="btn btn-light text-primary" data-bs-toggle="modal" data-bs-target="#addViolationModal">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route($prefix . 'violations.index') }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search name or student no.">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_reported" value="{{ request('date_reported') }}" class="form-control">
                        </div>
                        <div class="col-md-5 text-end">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="{{ route($prefix . 'violations.index') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($violations->isEmpty())
                        <p>No violation reports found.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Violation No</th>
                                        <th>Full Name</th>
                                        <th>Student No</th>
                                        <th>Email</th>
                                        <th>Date</th>
                                        <th>Offense</th>
                                        <th>Level</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($violations as $v)
                                        <tr>
                                            <td>{{ $v->violation_no }}</td>
                                            <td>{{ $v->full_name }}</td>
                                            <td>{{ $v->student_no }}</td>
                                            <td>{{ $v->student_email }}</td>
                                            <td>{{ $v->date_reported }}</td>
                                            <td>{{ $v->offense }}</td>
                                            <td>{{ $v->level }}</td>
                                            <td>{{ $v->status }}</td>
                                            <td>{{ $v->action_taken ?? 'N/A' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editViolationModal"
                                                    data-id="{{ $v->id }}"
                                                    data-name="{{ $v->full_name }}"
                                                    data-student="{{ $v->student_no }}"
                                                    data-email="{{ $v->student_email }}"
                                                    data-date="{{ $v->date_reported }}"
                                                    data-degree="{{ $v->yearlvl_degree }}"
                                                    data-offense="{{ $v->offense }}"
                                                    data-level="{{ $v->level }}"
                                                    data-status="{{ $v->status }}"
                                                    data-action="{{ $v->action_taken }}">
                                                    Edit
                                                </button>
                                                <form method="POST" action="{{ route($prefix . 'violations.destroy', $v->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $violations->withQueryString()->links() }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Add Violation Modal -->
        <div class="modal fade" id="addViolationModal" tabindex="-1">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route($prefix . 'violations.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Violation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="violation_no" value="{{ 'VIO-' . strtoupper(uniqid()) }}">
                        <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Student No.</label><input type="text" name="student_no" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="student_email" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Date Reported</label><input type="date" name="date_reported" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Year & Degree</label><input type="text" name="yearlvl_degree" class="form-control" required></div>
                        <div class="mb-3">
                            <label class="form-label">Offense</label>
                            <select name="offense" class="form-select" id="addOffenseSelect" required>
                                <optgroup label="Minor">
                                    @foreach($minorOffenses as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach
                                </optgroup>
                                <optgroup label="Major">
                                    @foreach($majorOffenses as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach
                                </optgroup>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Level</label>
                            <select name="level" class="form-select" id="addLevelSelect" required>
                                <option value="Minor">Minor</option>
                                <option value="Major">Major</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Pending">Pending</option>
                                <option value="Complete">Complete</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Action Taken</label>
                            <select name="action_taken" class="form-select">
                                <option value="">-- None --</option>
                                <option value="Warning">Warning</option>
                                <option value="DUSAP">DUSAP</option>
                                <option value="Suspension">Suspension</option>
                                <option value="Expulsion">Expulsion</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Submit</button></div>
                </form>
            </div>
        </div>

        <!-- Edit Violation Modal -->
        <div class="modal fade" id="editViolationModal" tabindex="-1">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" id="editViolationForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Violation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label>Full Name</label><input type="text" name="full_name" class="form-control" required></div>
                        <div class="mb-3"><label>Student No.</label><input type="text" name="student_no" class="form-control" required></div>
                        <div class="mb-3"><label>Email</label><input type="email" name="student_email" class="form-control" required></div>
                        <div class="mb-3"><label>Date</label><input type="date" name="date_reported" class="form-control" required></div>
                        <div class="mb-3"><label>Year & Degree</label><input type="text" name="yearlvl_degree" class="form-control" required></div>
                        <div class="mb-3">
                            <label>Offense</label>
                            <select name="offense" class="form-select" id="editOffenseSelect" required>
                                <optgroup label="Minor">@foreach($minorOffenses as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</optgroup>
                                <optgroup label="Major">@foreach($majorOffenses as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</optgroup>
                            </select>
                        </div>
                        <div class="mb-3"><label>Level</label>
                            <select name="level" class="form-select" id="editLevelSelect">
                                <option value="Minor">Minor</option>
                                <option value="Major">Major</option>
                            </select>
                        </div>
                        <div class="mb-3"><label>Status</label>
                            <select name="status" class="form-select">
                                <option value="Pending">Pending</option>
                                <option value="Complete">Complete</option>
                            </select>
                        </div>
                        <div class="mb-3"><label>Action</label>
                            <select name="action_taken" class="form-select">
                                <option value="">-- None --</option>
                                <option value="Warning">Warning</option>
                                <option value="DUSAP">DUSAP</option>
                                <option value="Suspension">Suspension</option>
                                <option value="Expulsion">Expulsion</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Update</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const minor = @json($minorOffenses);
        const major = @json($majorOffenses);

        const addOffense = document.getElementById('addOffenseSelect');
        const addLevel = document.getElementById('addLevelSelect');
        const editOffense = document.getElementById('editOffenseSelect');
        const editLevel = document.getElementById('editLevelSelect');

        function setLevel(select, target) {
            const value = select.value;
            if (minor.includes(value)) target.value = 'Minor';
            else if (major.includes(value)) target.value = 'Major';
        }

        addOffense?.addEventListener('change', () => setLevel(addOffense, addLevel));
        editOffense?.addEventListener('change', () => setLevel(editOffense, editLevel));

        document.getElementById('editViolationModal').addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            const form = document.getElementById('editViolationForm');
            form.action = `/admin/violations/${btn.dataset.id}`;
            form.querySelector('[name="full_name"]').value = btn.dataset.name;
            form.querySelector('[name="student_no"]').value = btn.dataset.student;
            form.querySelector('[name="student_email"]').value = btn.dataset.email;
            form.querySelector('[name="date_reported"]').value = btn.dataset.date;
            form.querySelector('[name="yearlvl_degree"]').value = btn.dataset.degree;
            form.querySelector('[name="offense"]').value = btn.dataset.offense;
            form.querySelector('[name="level"]').value = btn.dataset.level;
            form.querySelector('[name="status"]').value = btn.dataset.status;
            form.querySelector('[name="action_taken"]').value = btn.dataset.action;
        });
    });
</script>

@endsection
