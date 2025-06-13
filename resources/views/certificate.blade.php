@extends("layout.main")
@section("title", "Certificate Requests")
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
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Certificate Requests</h4>
                    <button class="btn btn-light text-primary" data-bs-toggle="modal" data-bs-target="#addCertificateModal">
                        <i class="fas fa-plus"></i> Add Request
                    </button>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{  'certificates.index') }}" class="row g-3 mb-4">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name">
                        </div>
                        <div class="col-md-4">
                            <select name="purpose" class="form-select">
                                <option value="">-- Filter by Purpose --</option>
                                <option value="Transfer" {{ request('purpose') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="Scholarship" {{ request('purpose') == 'Scholarship' ? 'selected' : '' }}>Scholarship</option>
                                <option value="Internship" {{ request('purpose') == 'Internship' ? 'selected' : '' }}>Internship</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex">
                            <button class="btn btn-primary me-2">Filter</button>
                            <a href="{{  'certificates.index') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ticket No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Student No</th>
                                    <th>Year & Degree</th>
                                    <th>Date</th>
                                    <th>Purpose</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($certificates as $certificate)
                                <tr>
                                    <td>{{ $certificate->ticket_no }}</td>
                                    <td>{{ $certificate->requester_name }}</td>
                                    <td>{{ $certificate->email }}</td>
                                    <td>{{ $certificate->student_no }}</td>
                                    <td>{{ $certificate->yearlvl_degree }}</td>
                                    <td>{{ $certificate->date_requested }}</td>
                                    <td>{{ $certificate->purpose }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning"
                                            data-bs-toggle="modal" data-bs-target="#editCertificateModal"
                                            data-id="{{ $certificate->id }}"
                                            data-requester="{{ $certificate->requester_name }}"
                                            data-email="{{ $certificate->email }}"
                                            data-studentno="{{ $certificate->student_no }}"
                                            data-degree="{{ $certificate->yearlvl_degree }}"
                                            data-date="{{ $certificate->date_requested }}"
                                            data-purpose="{{ $certificate->purpose }}">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-info"
                                            data-bs-toggle="modal" data-bs-target="#viewCertificateModal"
                                            data-ticket="{{ $certificate->ticket_no }}"
                                            data-name="{{ $certificate->requester_name }}"
                                            data-email="{{ $certificate->email }}"
                                            data-student="{{ $certificate->student_no }}"
                                            data-degree="{{ $certificate->yearlvl_degree }}"
                                            data-date="{{ $certificate->date_requested }}"
                                            data-purpose="{{ $certificate->purpose }}"
                                            data-id="{{ $certificate->id }}">
                                            View
                                        </button>
                                        <form method="POST" action="{{  'certificates.destroy', $certificate->id) }}" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this request?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $certificates->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCertificateModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{  'certificates.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Certificate Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2"><label>Name</label><input name="requester_name" class="form-control" required></div>
                <div class="mb-2"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-2"><label>Student No</label><input name="student_no" class="form-control" required></div>
                <div class="mb-2"><label>Year & Degree</label><input name="yearlvl_degree" class="form-control" required></div>
                <div class="mb-2"><label>Date Requested</label><input type="date" name="date_requested" class="form-control" required></div>
                <div class="mb-2"><label>Purpose</label>
                    <select name="purpose" class="form-select" required>
                        <option value="Transfer">Transfer</option>
                        <option value="Scholarship">Scholarship</option>
                        <option value="Internship">Internship</option>
                    </select>
                </div>
                <div class="text-end"><button class="btn btn-primary">Submit</button></div>
            </div>
        </form>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewCertificateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Certificate Request Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Ticket No:</strong> <span id="v_ticket"></span></p>
                <p><strong>Name:</strong> <span id="v_name"></span></p>
                <p><strong>Email:</strong> <span id="v_email"></span></p>
                <p><strong>Student No:</strong> <span id="v_student"></span></p>
                <p><strong>Year & Degree:</strong> <span id="v_degree"></span></p>
                <p><strong>Date Requested:</strong> <span id="v_date"></span></p>
                <p><strong>Purpose:</strong> <span id="v_purpose"></span></p>
                <div class="text-end mt-3">
                    <a id="v_pdf_link" href="#" class="btn btn-outline-primary" target="_blank">Generate PDF</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editCertificateModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editCertificateForm" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Certificate Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2"><label>Name</label><input name="requester_name" class="form-control" required></div>
                <div class="mb-2"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-2"><label>Student No</label><input name="student_no" class="form-control" required></div>
                <div class="mb-2"><label>Year & Degree</label><input name="yearlvl_degree" class="form-control" required></div>
                <div class="mb-2"><label>Date Requested</label><input type="date" name="date_requested" class="form-control" required></div>
                <div class="mb-2"><label>Purpose</label>
                    <select name="purpose" class="form-select" required>
                        <option value="Transfer">Transfer</option>
                        <option value="Scholarship">Scholarship</option>
                        <option value="Internship">Internship</option>
                    </select>
                </div>
                <div class="text-end"><button class="btn btn-primary">Update</button></div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editCertificateModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const form = editModal.querySelector('#editCertificateForm');
        const id = button.getAttribute('data-id');

        form.action = `/admin/certificates/update/${id}`;
        form.querySelector('[name="requester_name"]').value = button.getAttribute('data-requester');
        form.querySelector('[name="email"]').value = button.getAttribute('data-email');
        form.querySelector('[name="student_no"]').value = button.getAttribute('data-studentno');
        form.querySelector('[name="yearlvl_degree"]').value = button.getAttribute('data-degree');
        form.querySelector('[name="date_requested"]').value = button.getAttribute('data-date');
        form.querySelector('[name="purpose"]').value = button.getAttribute('data-purpose');
    });

    const viewModal = document.getElementById('viewCertificateModal');
    viewModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('v_ticket').innerText = button.getAttribute('data-ticket');
        document.getElementById('v_name').innerText = button.getAttribute('data-name');
        document.getElementById('v_email').innerText = button.getAttribute('data-email');
        document.getElementById('v_student').innerText = button.getAttribute('data-student');
        document.getElementById('v_degree').innerText = button.getAttribute('data-degree');
        document.getElementById('v_date').innerText = button.getAttribute('data-date');
        document.getElementById('v_purpose').innerText = button.getAttribute('data-purpose');
        document.getElementById('v_pdf_link').href = `/admin/certificates/${button.getAttribute('data-id')}/pdf`;
    });
});
</script>
@endsection
