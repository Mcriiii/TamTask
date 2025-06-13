@extends("layout.main")
@section("title", "Referral Reports")
@section("content")
<div class="top-navbar">
    <img src="{{ asset('images/logoo.png') }}" class="logo-nav" alt="">
    <div class="user-greeting">Hello, {{ Auth::user()->first_name }}</div>
</div>

<div class="main-wrapper">
    @include('layout.sidebar')

    <div class="main-content">
        <div class="container mt-5">
            <div class="card shadow rounded">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Referral Reports</h4>
                    <button type="button" class="btn btn-light text-success" data-bs-toggle="modal" data-bs-target="#addReferralModal">
                        <i class="fas fa-plus"></i> Add Referral
                    </button>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route( 'referrals.index') }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by referral number or role">
                        </div>
                        <div class="col-md-3">
                            <input type="datetime-local" name="date" class="form-control" value="{{ request('date') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="role" class="form-select">
                                <option value="">-- Role --</option>
                                <option value="Student" {{ request('role') == 'Student' ? 'selected' : '' }}>Student</option>
                                <option value="Teacher" {{ request('role') == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                                <option value="Associate" {{ request('role') == 'Associate' ? 'selected' : '' }}>Associate</option>
                                <option value="Security" {{ request('role') == 'Security' ? 'selected' : '' }}>Security</option>
                                <option value="SFU" {{ request('role') == 'SFU' ? 'selected' : '' }}>SFU</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success">Filter</button>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Referral No</th>
                                    <th>Date Reported</th>
                                    <th>Level</th>
                                    <th>Date to See</th>
                                    <th>Role</th>
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
                                    <td>{{ \Carbon\Carbon::parse($referral->date_to_see)->format('m/d/y h:i A') }}</td>
                                    <td>{{ $referral->role }}</td>
                                    <td class="text-{{ $referral->status == 'Complete' ? 'primary' : 'danger' }}">{{ $referral->status }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editReferralModal"
                                            data-id="{{ $referral->id }}"
                                            data-refno="{{ $referral->referral_no }}"
                                            data-date="{{ $referral->date_reported }}"
                                            data-level="{{ $referral->level }}"
                                            data-see="{{ $referral->date_to_see }}"
                                            data-role="{{ $referral->role }}"
                                            data-status="{{ $referral->status }}">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route( 'referrals.destroy', $referral->id) }}" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7">No referrals found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $referrals->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addReferralModal" tabindex="-1" aria-labelledby="addReferralModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route( 'referrals.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Referral</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="referral_no" value="{{ 'REF-' . strtoupper(uniqid()) }}">
                <div class="mb-2"><label>Date Reported</label><input type="datetime-local" name="date_reported" class="form-control" required></div>
                <div class="mb-2"><label>Level</label>
                    <select name="level" class="form-select" required>
                        <option value="Level 1">Level 1 - Less Serious</option>
                        <option value="Level 2">Level 2 - Moderate</option>
                        <option value="Level 3">Level 3 - Serious</option>
                    </select>
                </div>
                <div class="mb-2"><label>Date to See</label><input type="datetime-local" name="date_to_see" class="form-control" required></div>
                <div class="mb-2"><label>Role</label>
                    <select name="role" class="form-select" required>
                        <option value="Student">Student</option>
                        <option value="Teacher">Teacher</option>
                        <option value="Associate">Associate</option>
                        <option value="Security">Security</option>
                        <option value="SFU">SFU</option>
                    </select>
                </div>
                <div class="mb-2"><label>Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Pending">Pending</option>
                        <option value="Complete">Complete</option>
                    </select>
                </div>
                <div class="text-end"><button class="btn btn-success">Submit</button></div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editReferralModal" tabindex="-1" aria-labelledby="editReferralModalLabel" aria-hidden="true">
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
                <div class="mb-2"><label>Referral No</label><input class="form-control" name="referral_no" readonly></div>
                <div class="mb-2"><label>Date Reported</label><input type="datetime-local" name="date_reported" class="form-control" required></div>
                <div class="mb-2"><label>Level</label>
                    <select name="level" class="form-select" required>
                        <option value="Level 1">Level 1</option>
                        <option value="Level 2">Level 2</option>
                        <option value="Level 3">Level 3</option>
                    </select>
                </div>
                <div class="mb-2"><label>Date to See</label><input type="datetime-local" name="date_to_see" class="form-control" required></div>
                <div class="mb-2"><label>Role</label>
                    <select name="role" class="form-select" required>
                        <option value="Student">Student</option>
                        <option value="Teacher">Teacher</option>
                        <option value="Associate">Associate</option>
                        <option value="Security">Security</option>
                        <option value="SFU">SFU</option>
                    </select>
                </div>
                <div class="mb-2"><label>Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Pending">Pending</option>
                        <option value="Complete">Complete</option>
                    </select>
                </div>
                <div class="text-end"><button class="btn btn-primary">Update</button></div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editReferralModal');
    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const form = modal.querySelector('#editReferralForm');
        const id = button.getAttribute('data-id');

        form.action = `/referrals/update/${id}`;
        form.querySelector('[name="referral_no"]').value = button.getAttribute('data-refno');
        form.querySelector('[name="date_reported"]').value = formatDate(button.getAttribute('data-date'));
        form.querySelector('[name="date_to_see"]').value = formatDate(button.getAttribute('data-see'));
        form.querySelector('[name="level"]').value = button.getAttribute('data-level');
        form.querySelector('[name="role"]').value = button.getAttribute('data-role');
        form.querySelector('[name="status"]').value = button.getAttribute('data-status');
    });

    function formatDate(datetime) {
        const date = new Date(datetime);
        const offset = date.getTimezoneOffset();
        const local = new Date(date.getTime() - offset * 60000);
        return local.toISOString().slice(0, 16);
    }
});
</script>
@endsection
