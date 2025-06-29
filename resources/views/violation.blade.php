@extends("layout.main")
@section("title", "Violation Reports")
@section("content")

@php
$prefix = Auth::user()->role == 'admin' ? 'admin.' : '';
$routePrefix = $prefix ?: '';
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

<input type="hidden" id="modalError" value="{{ old('_modal') }}">

<style>
    .text-button {
        background: none;
        border: none;
        font-weight: bold;
        cursor: pointer;
        padding: 0;
        font-size: 0.9rem;
        text-decoration: underline;
    }

    .text-button:hover {
        opacity: 0.8;
        text-decoration: none;
    }

    .link-button {
        background: none;
        border: none;
        color: #006E44;
        /* FEU green */
        padding: 0;
        margin: 0;
        text-decoration: underline;
        cursor: pointer;
        font-weight: 500;
    }

    .link-button:hover {
        color: #004d3f;
        /* Slightly darker green */
        text-decoration: none;
    }

    .tooltip-hover {
        position: relative;
        cursor: pointer;
    }

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
        table-layout: auto;
    }

    .modern-table thead {
        background: #006E44;
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
<div class="page-wrapper d-flex">
    @include('layout.sidebar')
    <div class="content-wrapper flex-grow-1 d-flex flex-column">
        <div class="top-navbar">
            <div class="mx-auto">
                <img src="{{ asset('images/name_logo.png') }}" alt="" class="logo-nav">
            </div>
            <div class="user-greeting">
                Hello, {{ Auth::user()->first_name }}
            </div>
        </div>

        <div class="main-wrapper">
            <div class="main-content">

                <div class="card-header  text-black d-flex justify-content-between align-items-center" style="padding-bottom: 1.5rem;">
                    <h4 class="mb-0 fw-bold fs-3">Violation Reports</h4>
                    <button type="button" class="btn text-dark fw-bold" style="background-color: #FFD100; border: none;" data-bs-toggle="modal" data-bs-target="#addViolationModal">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif
                    <form method="GET" action="{{ route($routePrefix . 'violations.index') }}" class="row g-3 mb-4">
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
                        <div class="col-md-2 d-flex align-items-end gap-3">
                            <button type="submit" class="link-button">Filter</button>
                            <a href="{{ route($routePrefix . 'violations.index') }}" class="link-button">Clear</a>
                        </div>
                    </form>

                    <div class="modern-table-container">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Violation No</th>
                                    <th>Reported By</th>
                                    <th>Student No</th>
                                    <th>Offense</th>
                                    <th>Level</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Taken Action</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($violations as $v)
                                <tr>
                                    <td>{{ $v->violation_no }}</td>
                                    <td>
                                        @if($v->user)
                                        {{ $v->user->first_name }} {{ $v->user->last_name }}
                                        @else
                                        <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#"
                                            class="student-link"
                                            data-bs-toggle="modal"
                                            data-bs-target="#studentDetailsModal"
                                            data-studentno="{{ $v->student_no }}"
                                            data-fullname="{{ $v->full_name }}"
                                            data-email="{{ $v->student_email }}"
                                            data-yearlvl="{{ $v->yearlvl_degree }}"
                                            data-totalminors="{{ $stats->firstWhere('student_no', $v->student_no)?->total_minors }}"
                                            data-pendingminors="{{ $stats->firstWhere('student_no', $v->student_no)?->pending_minors }}"
                                            data-totalmajors="{{ $stats->firstWhere('student_no', $v->student_no)?->total_majors }}"
                                            data-pendingmajors="{{ $stats->firstWhere('student_no', $v->student_no)?->pending_majors }}">
                                            {{ $v->student_no }}
                                        </a>
                                    </td>
                                    <td>{{ $v->offense }}</td>
                                    <td>{{ $v->level }}</td>
                                    <td class="text-{{ $v->status == 'Complete' ? 'primary' : 'danger' }}">{{ $v->status }}</td>
                                    <td>{{ $v->date_reported }}</td>
                                    <td>@include('admin.takeaction_case', ['violation' => $v, 'prefix' => $prefix])</td>
                                    <td>
                                        <div class="d-flex flex-nowrap align-items-center gap-1 justify-content-center">
                                            <button type="button"
                                                class="text-button text-primary me-3"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editViolationModal"
                                                data-id="{{ $v->id }}"
                                                data-fullname="{{ $v->full_name }}"
                                                data-email="{{ $v->student_email }}"
                                                data-studentno="{{ $v->student_no }}"
                                                data-date="{{ $v->date_reported }}"
                                                data-degree="{{ $v->yearlvl_degree }}"
                                                data-offense="{{ $v->offense }}"
                                                data-action_taken="{{ $v->action_taken }}"
                                                data-status="{{ $v->status }}">
                                                Edit
                                            </button>

                                            <form action="{{ route($routePrefix . 'violations.destroy', $v->id) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirmDelete(this)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-button text-danger fw-bold">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9">No violations found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @include('summary', ['stats' => $stats])
                        {{ $violations->withQueryString()->links() }}
                    </div>
                </div>

            </div>
        </div>

        <!-- Add Modal -->
        <div class="modal fade" id="addViolationModal" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route( 'violations.store') }}" method="POST" class="modal-content">
                    @csrf
                    <input type="hidden" name="_modal" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Violation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Error Handling Inside Modal -->
                        @if(old('_modal') === 'add' && $errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Form Fields -->
                        <input type="hidden" name="violation_no" value="{{ 'VIO-' . strtoupper(uniqid()) }}">

                        <div class="mb-2">
                            <label>Full Name</label>
                            <input name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                            @if ($errors->has('full_name'))
                            <span class="text-danger">{{ $errors->first('full_name') }}</span>
                            @endif
                        </div>

                        <div class="mb-2">
                            <label>Student No</label>
                            <input name="student_no" class="form-control" value="{{ old('student_no') }}" required>
                            @if ($errors->has('student_no'))
                            <span class="text-danger">{{ $errors->first('student_no') }}</span>
                            @endif
                        </div>

                        <div class="mb-2">
                            <label>Email</label>
                            <input type="email" name="student_email" class="form-control" value="{{ old('student_email') }}" required>
                        </div>

                        <div class="mb-2">
                            <label>Date</label>
                            <input type="date" name="date_reported" class="form-control" value="{{ old('date_reported') }}" required>
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

                        <div class="mb-2">
                            <label>Offense</label>
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

                        <div class="mb-2">
                            <label>Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Complete" {{ old('status') == 'Complete' ? 'selected' : '' }}>Complete</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger">Submit</button>
                    </div>
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
                            action="{{ old('_modal') === 'edit' && session('edit_id') ? route( 'violations.update', session('edit_id')) : '' }}">
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

                            <div class="mb-3">
                                <label>Action Taken</label>
                                <select name="action_taken" class="form-select form-select-sm" required>
                                    <option value="" disabled>Select action</option>
                                    <optgroup label="General">
                                        <option value="Warning">Verbal/Written Warning</option>
                                        <option value="Parent/Guardian Conference">Parent/Guardian Conference</option>
                                    </optgroup>
                                    <optgroup label="Sanctions">
                                        <option value="Suspension">Suspension</option>
                                        <option value="Disciplinary Probation">Disciplinary Probation</option>
                                        <option value="DUSAP">DUSAP</option>
                                        <option value="Community Service">Community Service</option>
                                        <option value="Expulsion">Expulsion</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label>Offense</label>
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

        <!-- Student Details Modal -->
        <div class="modal fade" id="studentDetailsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Student Violation Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Student No:</strong> <span id="modalStudentNo"></span></p>
                        <p><strong>Full Name:</strong> <span id="modalFullName"></span></p>
                        <p><strong>Email:</strong> <span id="modalStudentEmail"></span></p>
                        <p><strong>Year & Degree:</strong> <span id="modalYearlvl"></span></p>
                        <hr>
                        <p><strong>Total Minors:</strong> <span id="modalTotalMinors"></span></p>
                        <p><strong>Pending Minor Violations:</strong> <span id="modalPendingMinors"></span></p>
                        <p><strong>Resolved Minor Violations:</strong> <span id="modalResolvedMinors"></span></p>
                        <p><strong>Total Majors:</strong> <span id="modalTotalMajors"></span></p>
                        <p><strong>Pending Major Violations:</strong> <span id="modalPendingMajors"></span></p>
                        <p><strong>Resolved Major Violations:</strong> <span id="modalResolvedMajors"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.student-link').forEach(el => {
            el.addEventListener('click', function() {
                const totalMinors = parseInt(this.dataset.totalminors) || 0;
                const pendingMinors = parseInt(this.dataset.pendingminors) || 0;
                const resolvedMinors = totalMinors - pendingMinors;

                const totalMajors = parseInt(this.dataset.totalmajors) || 0;
                const pendingMajors = parseInt(this.dataset.pendingmajors) || 0;
                const resolvedMajors = totalMajors - pendingMajors;

                document.getElementById('modalStudentNo').textContent = this.dataset.studentno;
                document.getElementById('modalFullName').textContent = this.dataset.fullname;
                document.getElementById('modalStudentEmail').textContent = this.dataset.email;
                document.getElementById('modalYearlvl').textContent = this.dataset.yearlvl;

                document.getElementById('modalTotalMinors').textContent = totalMinors;
                document.getElementById('modalPendingMinors').textContent = pendingMinors;
                document.getElementById('modalResolvedMinors').textContent = resolvedMinors;

                document.getElementById('modalTotalMajors').textContent = totalMajors;
                document.getElementById('modalPendingMajors').textContent = pendingMajors;
                document.getElementById('modalResolvedMajors').textContent = resolvedMajors;
            });
        });
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

            // Use prefix variable from Blade
            const prefix = "{{ $prefix }}"; // outputs 'admin.' or ''
            const base = prefix === 'admin.' ? '/admin' : ''; // convert to URL path

            form.action = `${base}/violations/update/${id}`;

            form.querySelector('[name="full_name"]').value = button.getAttribute('data-fullname');
            form.querySelector('[name="student_no"]').value = button.getAttribute('data-studentno');
            form.querySelector('[name="student_email"]').value = button.getAttribute('data-email');
            form.querySelector('[name="date_reported"]').value = button.getAttribute('data-date');
            form.querySelector('[name="offense"]').value = button.getAttribute('data-offense');
            form.querySelector('[name="status"]').value = button.getAttribute('data-status');
            form.querySelector('[name="action_taken"]').value = button.getAttribute('data-action_taken');
            // ✅ Properly select yearlvl_degree
            const yearDegree = button.getAttribute('data-degree');
            const select = form.querySelector('[name="yearlvl_degree"]');
            [...select.options].forEach(opt => {
                opt.selected = (opt.value === yearDegree);
            });
        });
    });

    function confirmDelete(form) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "This report will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });

        return false;
    }
</script>


@endsection