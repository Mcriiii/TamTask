@extends("layout.main")
@section("title", "Violation Reports")
@section("content")
@php
    $prefix = Auth::user()->role == 'admin' ? 'admin.' : '';

    $minorOffenses = [
        'No ID',
        'No Uniform',
        'Possession: Cigarette',
        'Simple Misconduct',
        'Smoking on Campus',
        'Eating in Restricted Area',
        'Public Intimacy',
        'Online Class Misconduct',
        'Other Misconduct'
    ];

    $majorOffenses = [
        'Prohibited Drugs',
        'Explosive Materials',
        'Deadly Weapon',
        'Under Influence',
        'Disrespect',
        'Defamation',
        'Discrimination',
        'Vandalism',
        'Dishonesty',
        'Hazing',
        'Bullying',
        'Sexual Harassment',
        'Bribery',
        'Cheating Exam',
        'Stealing',
        'Violence Against Women',
        'Plagiarism',
        'Falsification',
        'Misrepresentation'
    ];
@endphp

<!-- Top Navbar -->
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

<!-- Sidebar + Content -->
<div class="main-wrapper">
    @include('layout.sidebar')

    <div class="main-content">
        <div class="container mt-5">
            <div class="card shadow rounded">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Violation Reports</h4>
                    <button type="button" class="btn btn-light text-primary" data-bs-toggle="modal" data-bs-target="#addViolationModal">
                        <i class="fas fa-plus"></i> Add Report
                    </button>
                </div>
                <div class="card-body">
                    {{-- Filter --}}
                    <form method="GET" action="{{ route('violations.index') }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or student no.">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_reported" class="form-control" value="{{ request('date_reported') }}">
                        </div>
                        <div class="col-md-5 d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary me-1">Filter</button>
                                <a href="{{ route('violations.index') }}" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </form>

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    {{-- Violation Table --}}
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
                                    <th>Date Reported</th>
                                    <th>Offense</th>
                                    <th>Level</th>
                                    <th>Status</th>
                                    <th>Action Taken</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($violations as $violation)
                                <tr>
                                    <td>{{ $violation->violation_no }}</td>
                                    <td>{{ $violation->full_name }}</td>
                                    <td>{{ $violation->student_no }}</td>
                                    <td>{{ $violation->student_email }}</td>
                                    <td>{{ $violation->date_reported }}</td>
                                    <td>{{ $violation->offense }}</td>
                                    <td>{{ $violation->level }}</td>
                                    <td style="color: {{ $violation->status === 'Complete' ? 'blue' : 'red' }}">
                                        {{ $violation->status }}
                                    </td>
                                    <td>{{ $violation->action_taken ?? 'N/A' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary edit-btn"
                                            data-id="{{ $violation->id }}"
                                            data-violation_no="{{ $violation->violation_no }}"
                                            data-full_name="{{ $violation->full_name }}"
                                            data-student_no="{{ $violation->student_no }}"
                                            data-student_email="{{ $violation->student_email }}"
                                            data-date_reported="{{ $violation->date_reported }}"
                                            data-yearlvl_degree="{{ $violation->yearlvl_degree }}"
                                            data-offense="{{ $violation->offense }}"
                                            data-level="{{ $violation->level }}"
                                            data-status="{{ $violation->status }}"
                                            data-action_taken="{{ $violation->action_taken }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editViolationModal">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <form action="{{ route('violations.destroy', $violation->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this violation?');">
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
                    {{ $violations->withQueryString()->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Violation Modal -->
<div class="modal fade" id="addViolationModal" tabindex="-1" aria-labelledby="addViolationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
       <form action="{{ route('violations.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="addViolationModalLabel">Add Violation Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Student No.</label>
                    <input type="text" name="student_no" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Student Email</label>
                    <input type="email" name="student_email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date Reported</label>
                    <input type="date" name="date_reported" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Year Level & Degree</label>
                    <input type="text" name="yearlvl_degree" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Offense</label>
                    <select name="offense" class="form-select select2" required>
                        <optgroup label="Minor Offenses">
                            @foreach($minorOffenses as $minor)
                                <option value="{{ $minor }}">{{ $minor }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Major Offenses">
                            @foreach($majorOffenses as $major)
                                <option value="{{ $major }}">{{ $major }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Level</label>
                    <select name="level" class="form-select" required>
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
                    <label class="form-label">Action Taken (Optional)</label>
                    <select name="action_taken" class="form-select">
                        <option value="">-- None --</option>
                        <option value="Warning">Warning</option>
                        <option value="DUSAP">DUSAP</option>
                        <option value="Suspension">Suspension</option>
                        <option value="Expulsion">Expulsion</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>


<!-- Edit Violation Modal -->
<div class="modal fade" id="editViolationModal" tabindex="-1" aria-labelledby="editViolationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content" id="editViolationForm">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editViolationModalLabel">Edit Violation Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="editId">
                <div class="mb-3">
                    <label class="form-label">Violation No.</label>
                    <input type="text" name="violation_no" id="editViolationNo" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" id="editFullName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Student No.</label>
                    <input type="text" name="student_no" id="editStudentNo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Student Email</label>
                    <input type="email" name="student_email" id="editStudentEmail" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date Reported</label>
                    <input type="date" name="date_reported" id="editDateReported" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Year Level & Degree</label>
                    <input type="text" name="yearlvl_degree" id="editYearlvlDegree" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Offense</label>
                    <select name="offense" id="editOffense" class="form-select select2" required>
                        <optgroup label="Minor Offenses">
                            @foreach($minorOffenses as $minor)
                                <option value="{{ $minor }}">{{ $minor }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Major Offenses">
                            @foreach($majorOffenses as $major)
                                <option value="{{ $major }}">{{ $major }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Level</label>
                    <select name="level" id="editLevel" class="form-select" required>
                        <option value="Minor">Minor</option>
                        <option value="Major">Major</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" id="editStatus" class="form-select" required>
                        <option value="Pending">Pending</option>
                        <option value="Complete">Complete</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Action Taken (Optional)</label>
                    <select name="action_taken" id="editActionTaken" class="form-select">
                        <option value="">-- None --</option>
                        <option value="Warning">Warning</option>
                        <option value="DUSAP">DUSAP</option>
                        <option value="Suspension">Suspension</option>
                        <option value="Expulsion">Expulsion</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Other codes...

        $('.edit-btn').on('click', function() {
            $('#editId').val($(this).data('id'));
            $('#editViolationNo').val($(this).data('violation_no'));
            $('#editFullName').val($(this).data('full_name'));
            $('#editStudentNo').val($(this).data('student_no'));
            $('#editStudentEmail').val($(this).data('student_email'));
            $('#editDateReported').val($(this).data('date_reported'));
            $('#editYearlvlDegree').val($(this).data('yearlvl_degree'));

            // Select2 for Offense (Important: trigger 'change' for select2 to update)
            $('#editOffense').val($(this).data('offense')).trigger('change');

            $('#editLevel').val($(this).data('level'));
            $('#editStatus').val($(this).data('status'));
            $('#editActionTaken').val($(this).data('action_taken'));
        });

        // Setup Select2 for both modals
        $('.select2').select2({
            dropdownParent: $('#addViolationModal')
        });

        $('#editOffense').select2({
            dropdownParent: $('#editViolationModal')
        });

        // Form Submit for Edit
        $('#editViolationForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#editId').val();
            var url = "{{ url($prefix.'violations') }}/" + id;
            $(this).attr('action', url);
            this.submit();
        });
    });
</script>

@endsection
