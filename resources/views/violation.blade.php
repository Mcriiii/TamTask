@extends("layout.main")
@section("title", "Violation Reports")
@section("content")
@php
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
        <div class="card shadow rounded">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Violation Reports</h4>
                <button class="btn btn-light text-danger" data-bs-toggle="modal" data-bs-target="#addViolationModal">
                    <i class="fas fa-plus"></i> Add Violation
                </button>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                <input type="hidden" id="modalError" value="{{ old('_modal') }}">
                <form method="GET" action="{{ route('violations.index') }}" class="row g-3 mb-4">
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
                                <th style="width: 160px;">Action</th>
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
                                    <button class="btn btn-sm btn-warning me-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editViolationModal"
                                        data-id="{{ $v->id }}"
                                        data-fullname="{{ $v->full_name }}"
                                        data-email="{{ $v->student_email }}"
                                        data-studentno="{{ $v->student_no }}"
                                        data-date="{{ $v->date_reported }}"
                                        data-degree="{{ $v->yearlvl_degree }}"
                                        data-offense="{{ $v->offense }}"
                                        data-status="{{ $v->status }}">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('violations.destroy', $v->id) }}" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8">No violations found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $violations->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addViolationModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('violations.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="_modal" value="add">
            <div class="modal-header">
                <h5 class="modal-title">Add Violation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if(old('_modal') === 'add' && $errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <input type="hidden" name="violation_no" value="{{ 'VIO-' . strtoupper(uniqid()) }}">
                <div class="mb-2"><label>Full Name</label><input name="full_name" class="form-control" value="{{ old('full_name') }}" required></div>
                <div class="mb-2"><label>Student No</label><input name="student_no" class="form-control" value="{{ old('student_no') }}" required></div>
                <div class="mb-2"><label>Email</label><input type="email" name="student_email" class="form-control" value="{{ old('student_email') }}" required></div>
                <div class="mb-2"><label>Date</label><input type="date" name="date_reported" class="form-control" value="{{ old('date_reported') }}" required></div>
                <div class="mb-2"><label>Year & Degree</label><input name="yearlvl_degree" class="form-control" value="{{ old('yearlvl_degree') }}" required></div>
                <div class="mb-2"><label>Offense</label>
                    <select name="offense" class="form-select" required>
                        <optgroup label="Minor Offenses">
                            @foreach($minor as $offense)
                            <option {{ old('offense') == $offense ? 'selected' : '' }}>{{ $offense }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Major Offenses">
                            @foreach($major as $offense)
                            <option {{ old('offense') == $offense ? 'selected' : '' }}>{{ $offense }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="mb-2"><label>Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Complete" {{ old('status') == 'Complete' ? 'selected' : '' }}>Complete</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-danger">Submit</button></div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editViolationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Violation Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if(old('_modal') === 'edit' && $errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" id="editViolationForm"
                    action="{{ old('_modal') === 'edit' && session('edit_id') ? route('violations.update', session('edit_id')) : '' }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_modal" value="edit">

                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control"
                            value="{{ old('_modal') === 'edit' ? old('full_name') : '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Student Number</label>
                        <input type="text" name="student_no" class="form-control"
                            value="{{ old('_modal') === 'edit' ? old('student_no') : '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Student Email</label>
                        <input type="email" name="student_email" class="form-control"
                            value="{{ old('_modal') === 'edit' ? old('student_email') : '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Date Reported</label>
                        <input type="date" name="date_reported" class="form-control"
                            value="{{ old('_modal') === 'edit' ? old('date_reported') : '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Year Level & Degree</label>
                        <input type="text" name="yearlvl_degree" class="form-control"
                            value="{{ old('_modal') === 'edit' ? old('yearlvl_degree') : '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Offense</label>
                        <input type="text" name="offense" class="form-control"
                            value="{{ old('_modal') === 'edit' ? old('offense') : '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Pending" {{ old('_modal') === 'edit' && old('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Complete" {{ old('_modal') === 'edit' && old('status') === 'Complete' ? 'selected' : '' }}>Complete</option>
                        </select>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Update Violation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const alert = document.getElementById('success-alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);

        const modalError = document.getElementById('modalError')?.value;

        if (modalError === 'add') {
            new bootstrap.Modal(document.getElementById('addViolationModal')).show();
        }

        if (modalError === 'edit') {
            new bootstrap.Modal(document.getElementById('editViolationModal')).show();
        }

        const editModal = document.getElementById('editViolationModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            if (modalError === 'edit') return;

            const button = event.relatedTarget;
            const form = editModal.querySelector('#editViolationForm');
            const id = button.getAttribute('data-id');

            form.action = `/violations/update/${id}`;

            form.querySelector('[name="full_name"]').value = button.getAttribute('data-fullname');
            form.querySelector('[name="student_no"]').value = button.getAttribute('data-studentno');
            form.querySelector('[name="student_email"]').value = button.getAttribute('data-email');
            form.querySelector('[name="date_reported"]').value = button.getAttribute('data-date');
            form.querySelector('[name="yearlvl_degree"]').value = button.getAttribute('data-degree');
            form.querySelector('[name="offense"]').value = button.getAttribute('data-offense');
            form.querySelector('[name="status"]').value = button.getAttribute('data-status');
        });
    });
</script>
@endsection