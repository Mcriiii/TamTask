@extends("layout.main")
@section("title", "Complaints")
@section("content")
@php
$prefix = Auth::user()->role == 'admin' ? 'admin.' : '';
@endphp
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

    .btn-gradient-green {
        background: linear-gradient(to right, #38b000, #70e000);
        color: white;
        font-weight: 600;
        border: none;
        transition: 0.3s;
    }

    .btn-gradient-green:hover {
        background: linear-gradient(to right, #70e000, #38b000);
        transform: scale(1.02);
    }

    .btn-gradient-gray {
        background: linear-gradient(to right, rgb(157, 196, 235), #ced4da);
        color: rgb(59, 87, 114);
        font-weight: 600;
        border: none;
        transition: 0.3s;
    }

    .btn-gradient-gray:hover {
        background: linear-gradient(to right, #ced4da, #adb5bd);
        transform: scale(1.02);
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
<div class="page-wrapper d-flex">
    @include('layout.sidebar')
    <div class="content-wrapper flex-grow-1 d-flex flex-column">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="mx-auto">
                <img src="{{ asset('images/name_logo.png') }}" alt="" class="logo-nav">
            </div>
            <div class="user-greeting">
                Hello, {{ Auth::user()->first_name }}
            </div>
        </div>

        <!-- Sidebar + Content -->
        <div class="main-wrapper">

            <div class="main-content">


                <div class="card-header text-black d-flex justify-content-between align-items-center" style="padding-bottom: 1.5rem;">
                    <h4 class="mb-0 fw-bold fs-3">Complaint Reports</h4>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#selectRoleModal">
                        <i class="fas fa-plus"></i> Add
                    </button>
                    <!-- Role Selector Modal -->
                    <div class="modal fade" id="selectRoleModal" tabindex="-1" aria-labelledby="selectRoleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content shadow-lg rounded-4">
                                <div class="modal-header bg-success text-white rounded-top-4">
                                    <h5 class="modal-title">Select Reporter Type</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center px-4 py-5">
                                    <button class="btn btn-lg btn-gradient-green w-100 mb-3" onclick="showComplaintForm('student')">
                                        👨‍🎓 I'm a Student
                                    </button>
                                    <button class="btn btn-lg btn-gradient-gray w-100" onclick="showComplaintForm('teacher')">
                                        👨‍🏫 I'm a Teacher or Security
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route($prefix . 'complaints.index') }}" class="row g-3 align-items-end mb-4">
                        <div class="col"><input type="text" name="student_no" value="{{ request('student_no') }}" class="form-control" placeholder="Student No"></div>
                        <div class="col"><input type="date" name="date_reported" value="{{ request('date_reported') }}" class="form-control"></div>
                        <div class="col">
                            <select name="yearlvl_degree" class="form-select">
                                <option value="">All Years & Degrees</option>

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
                                    @foreach($grade11Strands as $strand)
                                    @php $value = "Grade 11 - $strand"; @endphp
                                    <option value="{{ $value }}" {{ request('yearlvl_degree') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </optgroup>

                                <optgroup label="Grade 12">
                                    @foreach($grade11Strands as $strand)
                                    @php $value = "Grade 12 - $strand"; @endphp
                                    <option value="{{ $value }}" {{ request('yearlvl_degree') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </optgroup>

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
                                        <option value="{{ $value }}" {{ request('yearlvl_degree') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                        @endfor
                                </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Ongoing" {{ request('status') == 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="Dismissed" {{ request('status') == 'Dismissed' ? 'selected' : '' }}>Dismissed</option>
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-select" id="editStudentSubjectSelect">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subj)
                                <option value="{{ $subj }}" {{ request('subject') == $subj ? 'selected' : '' }}>
                                    {{ $subj }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-3">
                            <button class="link-button">Filter</button>
                            <a href="{{ route($prefix . 'complaints.index') }}" class="link-button">Clear</a>
                        </div>
                    </form>



                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if($complaints->isEmpty())
                    <p>No complaints found.</p>
                    @else
                    <div class="modern-table-container">
                        <table class="modern-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ticket No</th>
                                    <th>Reporter</th>
                                    <th>Student No</th>
                                    <th>Date</th>
                                    <th>Year & Degree</th>
                                    <th>Subject</th>
                                    <th>Meeting</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($complaints as $complaint)
                                <tr>
                                    <td>{{ $complaint->ticket_no }}</td>
                                    <td>{{ $complaint->reporter_name }}</td>
                                    <td>{{ $complaint->student_no }}</td>
                                    <td>{{ $complaint->date_reported }}</td>
                                    <td>{{ Str::limit($complaint->yearlvl_degree, 15) }}</td>
                                    <td>{{ $complaint->subject }}</td>
                                    <td>{{ $complaint->meeting_schedule ? \Carbon\Carbon::parse($complaint->meeting_schedule)->format('M d, Y h:i A') : '—' }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                    $complaint->status === 'Resolved' ? 'success' : 
                                    ($complaint->status === 'Dismissed' ? 'danger' : 
                                    ($complaint->status === 'Ongoing' ? 'primary' : 'warning')) 
                                }}">
                                            {{ $complaint->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="text-button text-primary me-3" data-bs-toggle="modal"
                                            data-bs-target="#editComplaintModal" data-id="{{ $complaint->id }}"
                                            data-reporter="{{ $complaint->reporter_name }}"
                                            data-student="{{ $complaint->student_no }}"
                                            data-date="{{ $complaint->date_reported }}"
                                            data-degree="{{ $complaint->yearlvl_degree }}"
                                            data-subject="{{ $complaint->subject }}"
                                            @php
                                            $role='student' ;
                                            if (!$complaint->student_no) {
                                            $name = strtolower($complaint->reporter_name);
                                            $role = str_contains($name, 'security') ? 'security' : 'teacher';
                                            }
                                            @endphp
                                            data-role="{{ $role }}"
                                            data-meeting="{{ $complaint->meeting_schedule }}">
                                            Edit
                                        </button>
                                        <form action="{{ route($prefix .'complaints.destroy', $complaint->id) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirmDelete(this)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-button text-danger fw-bold">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $complaints->withQueryString()->links() }}
                    @endif
                </div>




                <!-- Add Complaint Modal -->
                <div class="modal fade" id="addComplaintModal" tabindex="-1" aria-labelledby="addComplaintModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route($prefix .'complaints.store') }}" method="POST" class="modal-content">
                            @csrf
                            <input type="hidden" name="_modal" value="add">
                            <input type="hidden" name="complaint_role" id="complaintRole"> <!-- Track who reported -->

                            <div class="modal-header">
                                <h5 class="modal-title">Add Complaint</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                @if($errors->any() && old('_modal') === 'add')
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <!-- Common field -->
                                <div class="mb-3">
                                    <label class="form-label">Reporter Name</label>
                                    <input type="text" name="reporter_name" class="form-control" required>
                                </div>

                                <!-- Student-only -->
                                <div class="mb-3" id="studentNoField">
                                    <label class="form-label">Student No.</label>
                                    <input type="text" name="student_no" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Date Reported</label>
                                    <input type="date" name="date_reported" class="form-control" required>
                                </div>

                                <div class="mb-3" id="yearDegreeField">
                                    <label class="form-label">Year & Degree</label>
                                    <select name="yearlvl_degree" class="form-select" required>
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
                                            @foreach($grade11Strands as $strand)
                                            @php $value = "Grade 11 - $strand"; @endphp
                                            <option value="{{ $value }}">{{ $value }}</option>
                                            @endforeach
                                        </optgroup>

                                        <optgroup label="Grade 12">
                                            @foreach($grade11Strands as $strand)
                                            @php $value = "Grade 12 - $strand"; @endphp
                                            <option value="{{ $value }}">{{ $value }}</option>
                                            @endforeach
                                        </optgroup>

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
                                                <option value="{{ $value }}">{{ $value }}</option>
                                                @endfor
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Student Subject -->
                                <div class="mb-3" id="studentSubject">
                                    <label class="form-label">Subject</label>
                                    <select name="subject" class="form-select">
                                        <option value="" disabled selected>Select what happened to you</option>
                                        <option>I was bullied</option>
                                        <option>I was verbally abused</option>
                                        <option>I was physically hurt</option>
                                        <option>I was threatened or intimidated</option>
                                        <option>I was harassed</option>
                                        <option>I was discriminated against</option>
                                        <option>I was unfairly treated by a teacher</option>
                                        <option>I was accused of something I didn't do</option>
                                        <option>I was cyberbullied</option>
                                        <option>Someone spread false rumors about me</option>
                                        <option>Someone posted about me without permission</option>
                                        <option>I was recorded or photographed without consent</option>
                                        <option>I was shouted at or embarrassed in public</option>
                                    </select>
                                </div>

                                <!-- Staff Subject -->
                                <div class="mb-3" id="staffSubject" style="display: none;">
                                    <label class="form-label">Subject</label>
                                    <textarea class="form-control" id="staffSubjectTextarea" rows="4" placeholder="Enter detailed complaint here..." name="subject"></textarea>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>


                <!-- Edit Complaint Modal -->
                <div class="modal fade" id="editComplaintModal" tabindex="-1" aria-labelledby="editComplaintModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Complaint</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">

                                <form method="POST" id="editForm">
                                    <input type="hidden" name="complaint_role" id="editComplaintRole">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="_modal" value="edit">
                                    @if($errors->any() && old('_modal') === 'edit')
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif



                                    <div class="mb-3">
                                        <label class="form-label">Reporter Name</label>
                                        <input type="text" name="reporter_name" class="form-control" value="{{ old('reporter_name') }}" required>
                                    </div>
                                    <div class="mb-3" id="editStudentNoField">
                                        <label class="form-label">Student No.</label>
                                        <input type="text" name="student_no" class="form-control" id="editStudentNo">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Date Reported</label>
                                        <input type="date" name="date_reported" class="form-control" value="{{ old('date_reported') }}" required>

                                    </div>
                                    <div class="mb-3" id="editYearDegreeField">
                                        <label class="form-label">Year & Degree</label>
                                        <select name="yearlvl_degree" class="form-select" id="editYearDegree">
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
                                                @foreach($grade11Strands as $strand)
                                                @php $value = "Grade 11 - $strand"; @endphp
                                                <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </optgroup>

                                            <optgroup label="Grade 12">
                                                @foreach($grade11Strands as $strand)
                                                @php $value = "Grade 12 - $strand"; @endphp
                                                <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </optgroup>

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
                                                    <option value="{{ $value }}">{{ $value }}</option>
                                                    @endfor
                                            </optgroup>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3" id="editStudentSubject">
                                        <label class="form-label">Subject</label>
                                        <select name="subject" class="form-select" id="editStudentSubjectSelect">
                                            <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Select what happened to you</option>
                                            <option value="I was bullied" {{ old('subject') == 'I was bullied' ? 'selected' : '' }}>I was bullied</option>
                                            <option value="I was verbally abused" {{ old('subject') == 'I was verbally abused' ? 'selected' : '' }}>I was verbally abused</option>
                                            <option value="I was physically hurt" {{ old('subject') == 'I was physically hurt' ? 'selected' : '' }}>I was physically hurt</option>
                                            <option value="I was threatened or intimidated" {{ old('subject') == 'I was threatened or intimidated' ? 'selected' : '' }}>I was threatened or intimidated</option>
                                            <option value="I was harassed" {{ old('subject') == 'I was harassed' ? 'selected' : '' }}>I was harassed</option>
                                            <option value="I was discriminated against" {{ old('subject') == 'I was discriminated against' ? 'selected' : '' }}>I was discriminated against</option>
                                            <option value="I was unfairly treated by a teacher" {{ old('subject') == 'I was unfairly treated by a teacher' ? 'selected' : '' }}>I was unfairly treated by a teacher</option>
                                            <option value="I was accused of something I didn't do" {{ old('subject') == "I was accused of something I didn't do" ? 'selected' : '' }}>I was accused of something I didn't do</option>
                                            <option value="I was cyberbullied" {{ old('subject') == 'I was cyberbullied' ? 'selected' : '' }}>I was cyberbullied</option>
                                            <option value="Someone spread false rumors about me" {{ old('subject') == 'Someone spread false rumors about me' ? 'selected' : '' }}>Someone spread false rumors about me</option>
                                            <option value="Someone posted about me without permission" {{ old('subject') == 'Someone posted about me without permission' ? 'selected' : '' }}>Someone posted about me without permission</option>
                                            <option value="I was recorded or photographed without consent" {{ old('subject') == 'I was recorded or photographed without consent' ? 'selected' : '' }}>I was recorded or photographed without consent</option>
                                            <option value="I was shouted at or embarrassed in public" {{ old('subject') == 'I was shouted at or embarrassed in public' ? 'selected' : '' }}>I was shouted at or embarrassed in public</option>
                                        </select>
                                    </div>

                                    <!-- Staff Subject -->
                                    <div class="mb-3" id="editStaffSubject" style="display: none;">
                                        <label class="form-label">Subject</label>
                                        <textarea class="form-control" rows="4" id="editStaffSubjectTextarea"
                                            placeholder="Enter detailed complaint here..."></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Meeting Schedule</label>
                                        <input type="datetime-local" name="meeting_schedule" class="form-control" id="editMeetingSchedule" value="{{ old('meeting_schedule') }}">
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const updateRouteTemplate = `{!! route($prefix . 'complaints.update', ['id' => 'COMPLAINT_ID']) !!}`;
</script>

<!-- Edit Modal Script -->
<script>
    function showComplaintForm(role) {
        const modal = new bootstrap.Modal(document.getElementById('addComplaintModal'));
        document.getElementById('complaintRole').value = role;

        const studentNo = document.querySelector('[name="student_no"]');
        const yearDegree = document.querySelector('[name="yearlvl_degree"]');

        const studentSubject = document.querySelector('#studentSubject select');
        const staffSubject = document.querySelector('#staffSubject textarea');

        if (role === 'student') {
            document.getElementById('studentNoField').style.display = 'block';
            document.getElementById('yearDegreeField').style.display = 'block';
            document.getElementById('studentSubject').style.display = 'block';
            document.getElementById('staffSubject').style.display = 'none';

            studentNo.setAttribute('required', 'required');
            yearDegree.setAttribute('required', 'required');

            // ✅ Show student subject field only
            studentSubject.setAttribute('name', 'subject');
            staffSubject.removeAttribute('name');
        } else if (role === 'teacher' || role === 'security') {
            document.getElementById('studentNoField').style.display = 'none';
            document.getElementById('yearDegreeField').style.display = 'none';
            document.getElementById('studentSubject').style.display = 'none';
            document.getElementById('staffSubject').style.display = 'block';

            studentNo.removeAttribute('required');
            yearDegree.removeAttribute('required');

            // ✅ Show staff subject field only
            staffSubject.setAttribute('name', 'subject');
            studentSubject.removeAttribute('name');
        }

        // Close role selector, open modal
        bootstrap.Modal.getInstance(document.getElementById('selectRoleModal')).hide();
        modal.show();
    }


    document.addEventListener('DOMContentLoaded', function() {
        const modalError = "{{ old('_modal') }}";
        if (modalError === 'add') {
            const addModal = new bootstrap.Modal(document.getElementById('addComplaintModal'));
            addModal.show();
        } else if (modalError === 'edit') {
            const editModal = new bootstrap.Modal(document.getElementById('editComplaintModal'));
            editModal.show();
        }
    });

    setTimeout(() => {
        const alert = document.getElementById('success-alert');
        if (alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 500);
        }
    }, 3000);

    const editModal = document.getElementById('editComplaintModal');

    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const role = button.getAttribute('data-role');
        document.getElementById('editComplaintRole').value = role;

        const studentNo = button.getAttribute('data-student');
        const yearDegree = button.getAttribute('data-degree');
        const subject = button.getAttribute('data-subject');
        const date = button.getAttribute('data-date');
        const meeting = button.getAttribute('data-meeting');
        const id = button.getAttribute('data-id');

        const form = editModal.querySelector('#editForm'); // ✅ Moved here
        form.action = updateRouteTemplate.replace('COMPLAINT_ID', id);

        const studentNoInput = form.querySelector('[name="student_no"]');
        const yearDegreeSelect = form.querySelector('[name="yearlvl_degree"]');

        form.querySelector('[name="reporter_name"]').value = button.getAttribute('data-reporter');
        studentNoInput.value = studentNo;
        form.querySelector('[name="date_reported"]').value = date;
        yearDegreeSelect.value = yearDegree;
        form.querySelector('[name="meeting_schedule"]').value = meeting;

        const isStudent = role === 'student';
        document.getElementById('editStudentNoField').style.display = isStudent ? 'block' : 'none';
        document.getElementById('editYearDegreeField').style.display = isStudent ? 'block' : 'none';
        document.getElementById('editStudentSubject').style.display = isStudent ? 'block' : 'none';
        document.getElementById('editStaffSubject').style.display = isStudent ? 'none' : 'block';

        if (!isStudent) {
            // Staff editing
            document.getElementById('editStudentSubjectSelect').removeAttribute('name');
            document.getElementById('editStaffSubjectTextarea').setAttribute('name', 'subject');
            document.getElementById('editStaffSubjectTextarea').value = subject;

            studentNoInput.removeAttribute('required');
            yearDegreeSelect.removeAttribute('required');
        } else {
            // Student editing
            document.getElementById('editStaffSubjectTextarea').removeAttribute('name');
            document.getElementById('editStudentSubjectSelect').setAttribute('name', 'subject');
            document.getElementById('editStudentSubjectSelect').value = subject;

            studentNoInput.setAttribute('required', 'required');
            yearDegreeSelect.setAttribute('required', 'required');
        }
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