@extends("layout.main")
@section("title", "Certificate Requests")
@section("content")

<style>
    /* Style for the file input */
    .custom-file-input {
        font-size: 0.8rem;
        /* Smaller font size */
        padding: 4px 8px;
        /* Smaller padding */
        border-radius: 8px;
        /* Rounded corners */
        width: 150px;
        /* Smaller width */
    }

    /* Style for the "No file chosen" text box */
    .custom-file-input:focus {
        outline: none;
        border-color: #28a745;
        /* Green border on focus */
    }

    /* Custom style for "N/A" text */
    .custom-na-text {
        font-size: 0.8rem;
        /* Smaller font size */
        padding: 4px 8px;
        /* Smaller padding */
        background-color: #f1f1f1;
        /* Light background */
        border-radius: 8px;
        /* Rounded corners */
        color: #6c757d;
        /* Gray text */
        display: inline-block;
        width: 150px;
        /* Smaller width */
        text-align: center;
        /* Center the text */
        border: 1px solid #ccc;
        /* Light border */
    }

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

    .modern-table .btn-info {
        background-color: #00b4d8;
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
            <h4 class="mb-0">Certificate Requests</h4>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCertificateModal">
                <i class="fas fa-plus"></i> Add Request
            </button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('certificates.index') }}" class="row g-3 mb-4">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name, student no, or ticket no">
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
                    <a href="{{ route('certificates.index') }}" class="btn btn-secondary">Clear</a>
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
                            <th>Receipt</th>
                            <th>Upload / Update</th>
                            <th>Student No</th>
                            <th>Year & Degree</th>
                            <th>Date</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($certificates as $certificate)
                        <tr>
                            <!-- 1. Ticket No -->
                            <td>{{ $certificate->ticket_no }}</td>

                            <!-- 2. Receipt View -->
                            <td>
                                @if ($certificate->receipt_path)
                                <button class="btn btn-outline-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewReceiptModal{{ $certificate->id }}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                @else
                                <span class="badge bg-secondary">No File</span>
                                @endif
                            </td>

                            <!-- 3. Upload or Update Form -->
                            <td>
                                @if ($certificate->status === 'Accepted')
                                <form method="POST" action="{{ route('certificates.uploadReceipt', $certificate->id) }}" enctype="multipart/form-data" class="d-flex justify-content-center align-items-center gap-2">
                                    @csrf
                                    <input type="file" name="receipt" accept="image/*" class="form-control form-control-sm custom-file-input" required>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-upload"></i>
                                    </button>
                                </form>
                                @elseif (in_array($certificate->status, ['Uploaded', 'Ready for Release', 'Released']))
                                <form method="POST" action="{{ route('certificates.updateFileStatus', $certificate->id) }}" class="d-flex justify-content-center align-items-center gap-2">
                                    @csrf @method('PUT')
                                    <select name="status" class="form-select form-select-sm w-auto" required>
                                        <option value="Uploaded" {{ $certificate->status == 'Uploaded' ? 'selected' : '' }}>Uploaded</option>
                                        <option value="Ready for Release" {{ $certificate->status == 'Ready for Release' ? 'selected' : '' }}>Ready</option>
                                        <option value="Released" {{ $certificate->status == 'Released' ? 'selected' : '' }}>Released</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                                @else
                                <span class="text-muted custom-na-text">N/A</span>
                                @endif
                            </td>


                            <!-- 4. Student No (❗️Missing before) -->
                            <td>{{ $certificate->student_no }}</td>

                            <!-- 5. Year & Degree -->
                            <td>{{ Str::limit($certificate->yearlvl_degree, 15) }}</td>

                            <!-- 6. Date -->
                            <td>{{ \Carbon\Carbon::parse($certificate->date_requested)->format('M d, Y') }}</td>

                            <!-- 7. Purpose -->
                            <td>{{ $certificate->purpose }}</td>

                            <!-- 8. Status Badge -->
                            <td>
                                @php
                                $color = match($certificate->status) {
                                'Pending' => 'secondary',
                                'Accepted' => 'primary',
                                'Uploaded' => 'warning',
                                'Ready for Release' => 'info',
                                'Released' => 'success',
                                'Declined' => 'danger',
                                default => 'dark'
                                };
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ $certificate->status }}</span>
                            </td>

                            <!-- 9. Actions -->
                            <td>
                                <!-- ✅ View Button -->
                                <button class="btn btn-sm btn-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewCertificateModal"
                                    data-id="{{ $certificate->id }}"
                                    data-ticket="{{ $certificate->ticket_no }}"
                                    data-name="{{ $certificate->requester_name }}"
                                    data-email="{{ $certificate->email }}"
                                    data-student="{{ $certificate->student_no }}"
                                    data-degree="{{ $certificate->yearlvl_degree }}"
                                    data-date="{{ $certificate->date_requested }}"
                                    data-purpose="{{ $certificate->purpose }}"
                                    data-status="{{ $certificate->status }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal" data-bs-target="#editCertificateModal"
                                    data-id="{{ $certificate->id }}"
                                    data-requester="{{ $certificate->requester_name }}"
                                    data-email="{{ $certificate->email }}"
                                    data-studentno="{{ $certificate->student_no }}"
                                    data-degree="{{ $certificate->yearlvl_degree }}"
                                    data-date="{{ $certificate->date_requested }}"
                                    data-purpose="{{ $certificate->purpose }}"
                                    data-status="{{ $certificate->status }}">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('certificates.destroy', $certificate->id) }}" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this request?')">Delete</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Receipt Modal -->
                        @if ($certificate->receipt_path)
                        <div class="modal fade" id="viewReceiptModal{{ $certificate->id }}" tabindex="-1">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content rounded-4 shadow-sm">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">Receipt - {{ $certificate->ticket_no }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body bg-light text-center">
                                        <img src="{{ asset('storage/' . $certificate->receipt_path) }}"
                                            class="w-100 rounded border shadow-sm"
                                            style="max-height: 90vh; object-fit: contain;"
                                            alt="Receipt for {{ $certificate->ticket_no }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </tbody>

                </table>

                {{ $certificates->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCertificateModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('certificates.store') }}" class="modal-content">
            @csrf
            <input type="hidden" name="_modal" value="add">
            <div class="modal-header">
                <h5 class="modal-title">Add Certificate Request</h5>
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
                <div class="mb-2"><label>Name</label><input name="requester_name" class="form-control" required
                        value="{{ old('_modal') === 'add' ? old('requester_name') : '' }}"></div>
                <div class="mb-2"><label>Email</label><input type="email" name="email" class="form-control" required
                        value="{{ old('_modal') === 'add' ? old('email') : '' }}">
                </div>
                <div class="mb-2"><label>Student No</label><input name="student_no" class="form-control" required
                        value="{{ old('_modal') === 'add' ? old('student_no') : '' }}">
                </div>
                <div class="mb-2">
                    <label>Year & Degree</label>
                    <select name="yearlvl_degree" class="form-select" required>
                        <!-- Grade 11 and 12 with strands -->
                        <optgroup label="Grade 11">
                            <option value="Grade 11 - ABM" {{ old('yearlvl_degree') == 'Grade 11 - ABM' ? 'selected' : '' }}>Grade 11 - ABM</option>
                            <option value="Grade 11 - ABM Specialization - Accountancy" {{ old('yearlvl_degree') == 'Grade 11 - ABM Specialization - Accountancy' ? 'selected' : '' }}>Grade 11 - ABM Specialization - Accountancy</option>
                            <option value="Grade 11 - ABM Specialization - Business Administration" {{ old('yearlvl_degree') == 'Grade 11 - ABM Specialization - Business Administration' ? 'selected' : '' }}>Grade 11 - ABM Specialization - Business Administration</option>
                            <option value="Grade 11 - STEM Specialization - Information Technology" {{ old('yearlvl_degree') == 'Grade 11 - STEM Specialization - Information Technology' ? 'selected' : '' }}>Grade 11 - STEM Specialization - Information Technology</option>
                            <option value="Grade 11 - STEM Specialization - Engineering" {{ old('yearlvl_degree') == 'Grade 11 - STEM Specialization - Engineering' ? 'selected' : '' }}>Grade 11 - STEM Specialization - Engineering</option>
                            <option value="Grade 11 - STEM Specialization - Health Allied" {{ old('yearlvl_degree') == 'Grade 11 - STEM Specialization - Health Allied' ? 'selected' : '' }}>Grade 11 - STEM Specialization - Health Allied</option>
                            <option value="Grade 11 - GAS" {{ old('yearlvl_degree') == 'Grade 11 - GAS' ? 'selected' : '' }}>Grade 11 - GAS</option>
                            <option value="Grade 11 - HUMSS" {{ old('yearlvl_degree') == 'Grade 11 - HUMSS' ? 'selected' : '' }}>Grade 11 - HUMSS</option>
                            <option value="Grade 11 - Sports Track" {{ old('yearlvl_degree') == 'Grade 11 - Sports Track' ? 'selected' : '' }}>Grade 11 - Sports Track</option>
                        </optgroup>

                        <optgroup label="Grade 12">
                            <option value="Grade 12 - ABM" {{ old('yearlvl_degree') == 'Grade 12 - ABM' ? 'selected' : '' }}>Grade 12 - ABM</option>
                            <option value="Grade 12 - ABM Specialization - Accountancy" {{ old('yearlvl_degree') == 'Grade 12 - ABM Specialization - Accountancy' ? 'selected' : '' }}>Grade 12 - ABM Specialization - Accountancy</option>
                            <option value="Grade 12 - ABM Specialization - Business Administration" {{ old('yearlvl_degree') == 'Grade 12 - ABM Specialization - Business Administration' ? 'selected' : '' }}>Grade 12 - ABM Specialization - Business Administration</option>
                            <option value="Grade 12 - STEM Specialization - Information Technology" {{ old('yearlvl_degree') == 'Grade 12 - STEM Specialization - Information Technology' ? 'selected' : '' }}>Grade 12 - STEM Specialization - Information Technology</option>
                            <option value="Grade 12 - STEM Specialization - Engineering" {{ old('yearlvl_degree') == 'Grade 12 - STEM Specialization - Engineering' ? 'selected' : '' }}>Grade 12 - STEM Specialization - Engineering</option>
                            <option value="Grade 12 - STEM Specialization - Health Allied" {{ old('yearlvl_degree') == 'Grade 12 - STEM Specialization - Health Allied' ? 'selected' : '' }}>Grade 12 - STEM Specialization - Health Allied</option>
                            <option value="Grade 12 - GAS" {{ old('yearlvl_degree') == 'Grade 12 - GAS' ? 'selected' : '' }}>Grade 12 - GAS</option>
                            <option value="Grade 12 - HUMSS" {{ old('yearlvl_degree') == 'Grade 12 - HUMSS' ? 'selected' : '' }}>Grade 12 - HUMSS</option>
                            <option value="Grade 12 - Sports Track" {{ old('yearlvl_degree') == 'Grade 12 - Sports Track' ? 'selected' : '' }}>Grade 12 - Sports Track</option>
                        </optgroup>

                        <!-- College Programs -->
                        @php
                        $courses = [
                        'BS Accountancy',
                        'BSBA Marketing & Multimedia Design',
                        'BSBA Financial Management & Business Analytics',
                        'BSBA Operations & Service Management',
                        'BS Computer Science (Software Engineering)',
                        'BSIT (Animation & Game Development)',
                        'BSIT (Cybersecurity)',
                        'BSIT (Web & Mobile Applications)',
                        'BS Psychology',
                        'BS Tourism Management',
                        ];
                        @endphp

                        @foreach($courses as $course)
                        <optgroup label="{{ $course }}">
                            @for ($i = 1; $i <= 4; $i++)
                                @php
                                $suffix=$i==1 ? 'st' : ($i==2 ? 'nd' : ($i==3 ? 'rd' : 'th' ));
                                $value="$i{$suffix} Year - $course" ;
                                @endphp
                                <option value="{{ $value }}" {{ old('yearlvl_degree') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                @endfor
                        </optgroup>
                        @endforeach
                    </select>
                </div>


                <div class="mb-2"><label>Date Requested</label><input type="date" name="date_requested" class="form-control" required
                        value="{{ old('_modal') === 'add' ? old('date_requested') : '' }}">
                </div>
                <div class="mb-2"><label>Purpose</label>
                    <select name="purpose" class="form-select" required>
                        <option value="Transfer" {{ old('_modal') === 'add' && old('purpose') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="Scholarship" {{ old('_modal') === 'add' && old('purpose') == 'Scholarship' ? 'selected' : '' }}>Scholarship</option>
                        <option value="Internship" {{ old('_modal') === 'add' && old('purpose') == 'Internship' ? 'selected' : '' }}>Internship</option>
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
                <p><strong>Status:</strong> <span id="v_status"></span></p>
                <div class="text-end mt-3" id="pdf_button_container" style="display: none;">
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
            <input type="hidden" name="_modal" value="edit">
            <div class="modal-header">
                <h5 class="modal-title">Edit Certificate Request</h5>
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
                <div class="mb-2"><label>Name</label><input name="requester_name" class="form-control" required
                        value="{{ old('_modal') === 'edit' ? old('requester_name') : '' }}">
                </div>
                <div class="mb-2"><label>Email</label><input type="email" name="email" class="form-control" required
                        value="{{ old('_modal') === 'edit' ? old('email') : '' }}">
                </div>
                <div class="mb-2"><label>Student No</label><input name="student_no" class="form-control" required
                        value="{{ old('_modal') === 'edit' ? old('student_no') : '' }}">
                </div>
                <div class="mb-2"><label>Year & Degree</label>
                    <select name="yearlvl_degree" class="form-select" required>

                        <!-- Grade 11 -->
                        <optgroup label="Grade 11">
                            @php
                            $grade11Strands = [
                            'ABM',
                            'ABM Specialization - Accountancy',
                            'ABM Specialization - Business Administration',
                            'STEM Specialization - Information Technology',
                            'STEM Specialization - Engineering',
                            'STEM Specialization - Health Allied',
                            'GAS',
                            'HUMSS',
                            'Sports Track',
                            ];
                            @endphp
                            @foreach ($grade11Strands as $strand)
                            @php $value = "Grade 11 - $strand"; @endphp
                            <option value="{{ $value }}" {{ old('_modal') === 'edit' && old('yearlvl_degree') == $value ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                            @endforeach
                        </optgroup>

                        <!-- Grade 12 -->
                        <optgroup label="Grade 12">
                            @php
                            $grade12Strands = $grade11Strands;
                            @endphp
                            @foreach ($grade12Strands as $strand)
                            @php $value = "Grade 12 - $strand"; @endphp
                            <option value="{{ $value }}" {{ old('_modal') === 'edit' && old('yearlvl_degree') == $value ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                            @endforeach
                        </optgroup>

                        <!-- College Courses -->
                        @php
                        $courses = [
                        'BS Accountancy',
                        'BSBA Marketing & Multimedia Design',
                        'BSBA Financial Management & Business Analytics',
                        'BSBA Operations & Service Management',
                        'BS Computer Science (Software Engineering)',
                        'BSIT (Animation & Game Development)',
                        'BSIT (Cybersecurity)',
                        'BSIT (Web & Mobile Applications)',
                        'BS Psychology',
                        'BS Tourism Management',
                        ];
                        @endphp

                        @foreach($courses as $course)
                        <optgroup label="{{ $course }}">
                            @for ($i = 1; $i <= 4; $i++)
                                @php
                                $suffix=$i==1 ? 'st' : ($i==2 ? 'nd' : ($i==3 ? 'rd' : 'th' ));
                                $value="$i{$suffix} Year - $course" ;
                                @endphp
                                <option value="{{ $value }}" {{ old('_modal') === 'edit' && old('yearlvl_degree') == $value ? 'selected' : '' }}>
                                {{ $value }}
                                </option>
                                @endfor
                        </optgroup>
                        @endforeach

                    </select>
                </div>


                <div class="mb-2"><label>Date Requested</label><input type="date" name="date_requested" class="form-control" required
                        value="{{ old('_modal') === 'edit' ? old('date_requested') : '' }}">
                </div>
                <div class="mb-2"><label>Purpose</label>
                    <select name="purpose" class="form-select" required>
                        <option value="Transfer" {{ old('_modal') === 'edit' && old('purpose') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="Scholarship" {{ old('_modal') === 'edit' && old('purpose') == 'Scholarship' ? 'selected' : '' }}>Scholarship</option>
                        <option value="Internship" {{ old('_modal') === 'edit' && old('purpose') == 'Internship' ? 'selected' : '' }}>Internship</option>
                    </select>

                </div>
                <div class="mb-2">
                    <label>Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Pending" {{ old('_modal') === 'edit' && old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Accepted" {{ old('_modal') === 'edit' && old('status') == 'Accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="Declined" {{ old('_modal') === 'edit' && old('status') == 'Declined' ? 'selected' : '' }}>Declined</option>
                    </select>

                </div>
                <div class="text-end"><button class="btn btn-primary">Update</button></div>
            </div>
        </form>
    </div>
</div>
<input type="hidden" id="modalError" value="{{ old('_modal') }}">
<input type="hidden" id="editId" value="{{ old('edit_id') }}">
<input type="hidden" id="receiptErrorId" value="{{ old('_receipt_error') }}">


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const receiptErrorId = document.getElementById('receiptErrorId')?.value;
        if (receiptErrorId) {
            const receiptModal = new bootstrap.Modal(document.getElementById(`viewReceiptModal${receiptErrorId}`));
            receiptModal.show();
        }

        const modalError = document.getElementById('modalError')?.value;

        if (modalError === 'add') {
            new bootstrap.Modal(document.getElementById('addCertificateModal')).show();
        }

        if (modalError === 'edit') {
            new bootstrap.Modal(document.getElementById('editCertificateModal')).show();
        }

        setTimeout(() => {
            const alert = document.getElementById('success-alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);

        const editModal = document.getElementById('editCertificateModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const form = editModal.querySelector('#editCertificateForm');
            const id = button.getAttribute('data-id');

            // 🛠 Set form action
            form.action = `{{ url('certificates') }}/${id}/update`;

            // 🔍 Only populate if no validation error
            const isValidationError = "{{ old('_modal') }}" === "edit";

            if (!isValidationError) {
                form.querySelector('[name="requester_name"]').value = button.getAttribute('data-requester');
                form.querySelector('[name="email"]').value = button.getAttribute('data-email');
                form.querySelector('[name="student_no"]').value = button.getAttribute('data-studentno');
                form.querySelector('[name="date_requested"]').value = button.getAttribute('data-date');
                form.querySelector('[name="purpose"]').value = button.getAttribute('data-purpose');
                form.querySelector('[name="status"]').value = button.getAttribute('data-status');

                // Year & Degree
                const yearDegree = button.getAttribute('data-degree');
                const select = form.querySelector('[name="yearlvl_degree"]');
                [...select.options].forEach(opt => {
                    opt.selected = (opt.value === yearDegree);
                });
            }
        });

        const viewModal = document.getElementById('viewCertificateModal');
        viewModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const status = button.getAttribute('data-status');
            document.getElementById('v_ticket').innerText = button.getAttribute('data-ticket');
            document.getElementById('v_name').innerText = button.getAttribute('data-name');
            document.getElementById('v_email').innerText = button.getAttribute('data-email');
            document.getElementById('v_student').innerText = button.getAttribute('data-student');
            document.getElementById('v_degree').innerText = button.getAttribute('data-degree');
            document.getElementById('v_date').innerText = button.getAttribute('data-date');
            document.getElementById('v_purpose').innerText = button.getAttribute('data-purpose');
            document.getElementById('v_status').innerText = status;

            const pdfContainer = document.getElementById('pdf_button_container');
            if (status === "Ready for Release" || status === "Released") {
                pdfContainer.style.display = "block";
                document.getElementById('v_pdf_link').href = `/admin/certificates/${button.getAttribute('data-id')}/pdf`;
            } else {
                pdfContainer.style.display = "none";
            }
        });

    });
</script>


@endsection