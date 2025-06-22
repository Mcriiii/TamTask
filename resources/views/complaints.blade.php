@extends("layout.main")
@section("title", "Complaints")
@section("content")

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
<!-- Top Navbar -->
<div class="top-navbar">
    <img src="{{ asset('images/logoo.png') }}" alt="" class="logo-nav">
    <div class="user-greeting">Hello, Admin</div>
</div>

<!-- Sidebar + Content -->
<div class="main-wrapper">
    @include('layout.sidebar')
    <div class="main-content">


        <div class="card-header text-black d-flex justify-content-between align-items-center" style="padding-bottom: 1.5rem;">
            <h4 class="mb-0">Complaint Reports</h4>
            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                data-bs-target="#addComplaintModal">
                <i class="fas fa-plus"></i> Add
            </button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('complaints.index') }}" class="row g-3 align-items-end mb-4">
                <div class="col"><input type="text" name="student_no" value="{{ request('student_no') }}" class="form-control" placeholder="Student No"></div>
                <div class="col"><input type="date" name="date_reported" value="{{ request('date_reported') }}" class="form-control"></div>
                <div class="col"><input type="text" name="yearlvl_degree" value="{{ request('yearlvl_degree') }}" class="form-control" placeholder="Year & Degree"></div>
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
                    <select name="subject" class="form-select">
                        <option value="">Select subject</option>
                        <option value="Bullying" {{ request('subject') == 'Bullying' ? 'selected' : '' }}>Bullying</option>
                        <option value="Harassment" {{ request('subject') == 'Harassment' ? 'selected' : '' }}>Harassment</option>
                        <option value="Discrimination" {{ request('subject') == 'Discrimination' ? 'selected' : '' }}>Discrimination</option>
                    </select>
                </div>
                <div class="col d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('complaints.index') }}" class="btn btn-secondary">Clear</a>
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
                            <td>{{ $complaint->yearlvl_degree }}</td>
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
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#editComplaintModal" data-id="{{ $complaint->id }}"
                                    data-reporter="{{ $complaint->reporter_name }}"
                                    data-student="{{ $complaint->student_no }}"
                                    data-date="{{ $complaint->date_reported }}"
                                    data-degree="{{ $complaint->yearlvl_degree }}"
                                    data-subject="{{ $complaint->subject }}"
                                    data-meeting="{{ $complaint->meeting_schedule }}">
                                    Edit
                                </button>
                                <form action="{{ route('complaints.destroy', $complaint->id) }}" method="POST"
                                    style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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
        <div class="modal fade" id="addComplaintModal" tabindex="-1" aria-labelledby="addComplaintModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Complaint</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('complaints.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_modal" value="add">
                            @if($errors->any() && old('_modal') === 'add')
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
                                <input type="text" name="reporter_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Student No.</label>
                                <input type="text" name="student_no" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date Reported</label>
                                <input type="date" name="date_reported" class="form-control" required>
                            </div>
                            <div class="mb-3">
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
                                        <option value="{{ $value }}" {{ old('yearlvl_degree') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </optgroup>

                                    <optgroup label="Grade 12">
                                        @foreach($grade11Strands as $strand)
                                        @php $value = "Grade 12 - $strand"; @endphp
                                        <option value="{{ $value }}" {{ old('yearlvl_degree') == $value ? 'selected' : '' }}>{{ $value }}</option>
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
                                            <option value="{{ $value }}" {{ old('yearlvl_degree') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                            @endfor
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <select name="subject" class="form-select" required>
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
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
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
                            <div class="mb-3">
                                <label class="form-label">Student No.</label>
                                <input type="text" name="student_no" class="form-control" value="{{ old('student_no') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date Reported</label>
                                <input type="date" name="date_reported" class="form-control" value="{{ old('date_reported') }}" required>

                            </div>
                            <div class="mb-3">
                                <label class="form-label">Year & Degree</label>
                                <select name="yearlvl_degree" class="form-select" id="editYearDegree" required>
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

                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <select name="subject" class="form-select" required>
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

<!-- Edit Modal Script -->
<script>
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
        const id = button.getAttribute('data-id');
        const reporter = button.getAttribute('data-reporter');
        const student = button.getAttribute('data-student');
        const date = button.getAttribute('data-date');
        const degree = button.getAttribute('data-degree');
        const subject = button.getAttribute('data-subject');

        const form = editModal.querySelector('#editForm');
        form.action = `{{ route('complaints.update', ['id' => '__ID__']) }}`.replace('__ID__', id);
        form.querySelector('[name="reporter_name"]').value = reporter;
        form.querySelector('[name="student_no"]').value = student;
        form.querySelector('[name="date_reported"]').value = date;
        form.querySelector('[name="yearlvl_degree"]').value = degree;
        form.querySelector('[name="subject"]').value = subject;
        form.querySelector('[name="meeting_schedule"]').value = button.getAttribute('data-meeting');
    });
</script>
@endsection