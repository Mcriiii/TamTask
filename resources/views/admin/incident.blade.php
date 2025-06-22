@extends("layout.main")
@section("title", "Incident Reports")
@section("content")
@php
$prefix = Auth::user()->role == 'admin' ? 'admin.' : '';
@endphp
<style>
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
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
        transform: scale(1.01);
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
            
                <div class="card-header  text-black d-flex justify-content-between align-items-center"style="padding-bottom: 1.5rem;">
                    <h4 class="mb-0">Incident Reports</h4>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addIncidentModal">
                        <i class="fas fa-plus"></i> Add Incident
                    </button>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route($prefix . 'incidents.index') }}" class="row g-3 mb-4">
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

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <div class="modern-table-container">
                        <table class="modern-table">
                            <thead>
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
                                        <button type="button" class="btn btn-sm btn-warning me-1"
                                            data-bs-toggle="modal" data-bs-target="#editIncidentModal"
                                            data-id="{{ $incident->id }}"
                                            data-incident="{{ $incident->incident }}"
                                            data-reporter="{{ $incident->reporter_name }}"
                                            data-date="{{ $incident->date_reported }}"
                                            data-role="{{ $incident->reporter_role }}"
                                            data-status="{{ $incident->status }}">
                                            Edit
                                        </button>
                                        <form action="{{ route($prefix .'incidents.destroy', $incident->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this report?');">
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
                </div>
            
        
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addIncidentModal" tabindex="-1" aria-labelledby="addIncidentModalLabel" aria-hidden="true"> 
    <div class="modal-dialog">
        <form method="POST" action="{{ route($prefix . 'incidents.store') }}" class="modal-content">
            @csrf
            <input type="hidden" name="_modal" value="add">
            <div class="modal-header">
                <h5 class="modal-title">Add Incident Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="ticket_no" value="{{ 'INC-' . strtoupper(uniqid()) }}"> <!-- ✅ Required -->

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
                <div class="text-end">
                    <button class="btn btn-danger">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>



<!-- Edit Modal -->
<div class="modal fade" id="editIncidentModal" tabindex="-1" aria-labelledby="editIncidentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="editIncidentForm" class="modal-content">
            @csrf
            @method('PUT')
            <input type="hidden" name="_modal" value="edit">
            <div class="modal-header">
                <h5 class="modal-title">Edit Incident</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
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
                <div class="text-end">
                    <button class="btn btn-danger">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalError = document.getElementById('modalError')?.value;

        if (modalError === 'add') {
            new bootstrap.Modal(document.getElementById('addIncidentModal')).show();
        }

        if (modalError === 'edit') {
            new bootstrap.Modal(document.getElementById('editIncidentModal')).show();
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const alert = document.getElementById('success-alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);

        const editModal = document.getElementById('editIncidentModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const form = editModal.querySelector('#editIncidentForm');

            const id = button.getAttribute('data-id');
            form.action = `{{ route($prefix . 'incidents.update', ['id' => '__id']) }}`.replace('__id', id);
            form.querySelector('[name="incident"]').value = button.getAttribute('data-incident');
            form.querySelector('[name="reporter_name"]').value = button.getAttribute('data-reporter');
            form.querySelector('[name="date_reported"]').value = button.getAttribute('data-date');
            form.querySelector('[name="reporter_role"]').value = button.getAttribute('data-role');
            form.querySelector('[name="status"]').value = button.getAttribute('data-status');
        });
    });
</script>
<input type="hidden" id="modalError" value="{{ old('_modal') }}">
@endsection