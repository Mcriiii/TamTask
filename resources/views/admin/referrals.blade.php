@extends("layout.main")
@section("title", "Referral Reports")
@section("content")
@php
$prefix = Auth::user()->role == 'admin' ? 'admin.' : '';
$routePrefix = $prefix ?: '';
@endphp
<style>
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
        table-layout: auto;
    }

    .modern-table thead {
        background: linear-gradient(to right, #38b000, rgb(84, 160, 7));
        color: #fff;
    }

    .modern-table th {
        padding: 12px 16px;
        text-align: center;
        font-weight: 600;
    }

    .modern-table tbody tr {
        background-color: rgba(254, 255, 240, 0.9);
        border-radius: 999px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .modern-table td {
        padding: 12px 16px;
        text-align: center;
        border: none;
    }

    .modern-table tbody tr td:first-child {
        border-top-left-radius: 999px;
        border-bottom-left-radius: 999px;
    }

    .modern-table tbody tr td:last-child {
        border-top-right-radius: 999px;
        border-bottom-right-radius: 999px;
    }

    .modern-table tbody tr:hover {
        background-color: rgba(242, 194, 0, 0.25);
        transform: scale(1.001);
        transition: all 0.2s ease-in-out;
    }

    .modern-table .btn {
        padding: 4px 10px;
        font-size: 0.8rem;
        border-radius: 8px;
    }

    .modern-table .btn-warning {
        background-color: #ffcc00;
        border: none;
    }

    .modern-table .btn-success {
        background-color: #38b000;
        border: none;
    }

    .modern-table .btn-danger {
        background-color: #e63946;
        border: none;
    }
</style>
<div class="top-navbar">
    <img src="{{ asset('images/logoo.png') }}" class="logo-nav" alt="">
    <div class="user-greeting">Hello, {{ Auth::user()->first_name }}</div>
</div>

<div class="main-wrapper">
    @include('layout.sidebar')

    <div class="main-content">
        <div class="card-header text-black d-flex justify-content-between align-items-center" style="padding-bottom: 1.5rem;">
            <h4 class="mb-0">Referral Reports</h4>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addReferralModal">
                <i class="fas fa-plus"></i> Add Referral
            </button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route($prefix . 'referrals.index') }}" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by referral number or student name">
                </div>
                <div class="col-md-3">
                    <input type="datetime-local" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success">Filter</button>
                </div>
            </form>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" id="success-alert" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="modern-table-container">
                <table class="modern-table">
                    <thead class="table-dark">
                        <tr>
                            <th>Referral No</th>
                            <th>Date Reported</th>
                            <th>Level</th>
                            <th>Student Name</th>
                            <th>Date to See</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($referrals as $referral)
                        <tr>
                            <td>{{ $referral->referral_no }}</td>
                            <td>{{ \Carbon\Carbon::parse($referral->date_reported)->format('m/d/y h:i A') }}</td>
                            <td>{{ $referral->level }}</td>
                            <td>{{ $referral->student_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($referral->date_to_see)->format('m/d/y h:i A') }}</td>
                            <td class="text-{{ $referral->status == 'Complete' ? 'primary' : 'danger' }}">{{ $referral->status }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editReferralModal"
                                    data-id="{{ $referral->id }}"
                                    data-refno="{{ $referral->referral_no }}"
                                    data-date="{{ $referral->date_reported }}"
                                    data-level="{{ $referral->level }}"
                                    data-student_name="{{ $referral->student_name }}"
                                    data-see="{{ $referral->date_to_see }}"
                                    data-status="{{ $referral->status }}">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route($prefix . 'referrals.destroy', $referral->id) }}" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">No referrals found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $referrals->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
<!-- Add Modal -->
<div class="modal fade" id="addReferralModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route($prefix . 'referrals.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Referral</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Show error message if any -->
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <input type="hidden" name="referral_no" value="{{ 'REF-' . strtoupper(uniqid()) }}">

                <div class="mb-2">
                    <label>Date Reported</label>
                    <input type="datetime-local" name="date_reported" class="form-control" value="{{ old('date_reported') }}" required>
                </div>

                <div class="mb-2">
                    <label>Level</label>
                    <select name="level" class="form-select" required>
                        <option value="Level 1" {{ old('level') == 'Level 1' ? 'selected' : '' }}>Level 1 - Less Serious</option>
                        <option value="Level 2" {{ old('level') == 'Level 2' ? 'selected' : '' }}>Level 2 - Moderate</option>
                        <option value="Level 3" {{ old('level') == 'Level 3' ? 'selected' : '' }}>Level 3 - Serious</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label>Student Name</label>
                    <input type="text" name="student_name" class="form-control" value="{{ old('student_name') }}" required>
                </div>

                <div class="mb-2">
                    <label>Date to See</label>
                    <input type="datetime-local" name="date_to_see" class="form-control" value="{{ old('date_to_see') }}" required>
                </div>

                <div class="text-end">
                    <button class="btn btn-success">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editReferralModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="editReferralForm" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Referral</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="referral_id">

                <!-- Show error message if any -->
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="mb-2">
                    <label>Referral No</label>
                    <input class="form-control" name="referral_no" readonly value="{{ old('referral_no') }}">
                </div>

                <div class="mb-2">
                    <label>Date Reported</label>
                    <input type="datetime-local" name="date_reported" class="form-control" value="{{ old('date_reported') }}" required>
                </div>

                <div class="mb-2">
                    <label>Level</label>
                    <select name="level" class="form-select" required>
                        <option value="Level 1" {{ old('level') == 'Level 1' ? 'selected' : '' }}>Level 1</option>
                        <option value="Level 2" {{ old('level') == 'Level 2' ? 'selected' : '' }}>Level 2</option>
                        <option value="Level 3" {{ old('level') == 'Level 3' ? 'selected' : '' }}>Level 3</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label>Student Name</label>
                    <input type="text" name="student_name" class="form-control" value="{{ old('student_name') }}" required>
                </div>

                <div class="mb-2">
                    <label>Date to See</label>
                    <input type="datetime-local" name="date_to_see" class="form-control" value="{{ old('date_to_see') }}" required>
                </div>

                <div class="mb-2">
                    <label>Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Complete" {{ old('status') == 'Complete' ? 'selected' : '' }}>Complete</option>
                    </select>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let addModal, editModal;

        // Check if there are validation errors and show the correct modal
        @if ($errors->any())
            @if(request()->is($prefix . 'referrals.create'))
                // If it's a create request and there are validation errors, open the Add Referral Modal
                addModal = new bootstrap.Modal(document.getElementById('addReferralModal'));
                addModal.show();
            @elseif(request()->is($prefix . 'referrals.edit'))
                // If it's an edit request and there are validation errors, open the Edit Referral Modal
                editModal = new bootstrap.Modal(document.getElementById('editReferralModal'));
                editModal.show();
            @endif
        @endif

        // Open the Edit Modal when the "Edit" button is clicked and pre-fill the form with data
        editModal = document.getElementById('editReferralModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget; // The button that triggered the modal
                const form = editModal.querySelector('#editReferralForm');
                const id = button.getAttribute('data-id'); // Get the ID from the data-* attribute

                // Construct the form action URL
                form.action = "{{ route($routePrefix . 'referrals.update', ['id' => '__ID__']) }}".replace('__ID__', id);

                // Pre-fill the form with data from the button or old input (if validation failed)
                form.querySelector('[name="referral_no"]').value = button.getAttribute('data-refno') || "{{ old('referral_no') }}";
                form.querySelector('[name="date_reported"]').value = button.getAttribute('data-date') || "{{ old('date_reported') }}";
                form.querySelector('[name="level"]').value = button.getAttribute('data-level') || "{{ old('level') }}";
                form.querySelector('[name="student_name"]').value = button.getAttribute('data-student_name') || "{{ old('student_name') }}";
                form.querySelector('[name="date_to_see"]').value = button.getAttribute('data-see') || "{{ old('date_to_see') }}";
                form.querySelector('[name="status"]').value = button.getAttribute('data-status') || "{{ old('status') }}";
            });
        }

        // Open the Add Modal when there are validation errors after a failed store request
        addModal = document.getElementById('addReferralModal');
        if (addModal) {
            @if(session('_modal') === 'add')
                addModal = new bootstrap.Modal(document.getElementById('addReferralModal'));
                addModal.show();
            @endif
        }

        // Automatically close the success alert after 3 seconds
        setTimeout(() => {
            const alert = document.getElementById('success-alert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    });
</script>



@endsection