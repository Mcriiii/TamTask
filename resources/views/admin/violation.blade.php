@extends("layout.main")
@section("title", "Violation Reports")
@section("content")
    @php
        $prefix = Auth::user()->role === 'admin' ? 'admin.' : '';
    @endphp

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

    <div class="main-wrapper">
        @include('layout.sidebar')

        <div class="main-content">
            <div class="container mt-5">
                <div class="card shadow rounded">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Violation Reports</h4>
                        <button type="button" class="btn btn-light text-primary" data-bs-toggle="modal"
                            data-bs-target="#addViolationModal">
                            <i class="fas fa-plus"></i> Add Report
                        </button>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route($prefix . 'violations.index') }}" class="row g-3 mb-4">
                            <div class="col-md-4">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="Search by name or student no.">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date_reported" class="form-control"
                                    value="{{ request('date_reported') }}">
                            </div>
                            <div class="col-md-5 d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary me-1">Filter</button>
                                <a href="{{ route($prefix . 'violations.index') }}" class="btn btn-secondary">Clear</a>
                            </div>
                        </form>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

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
                                        @foreach($violations as $v)
                                            <tr>
                                                <td>{{ $v->violation_no }}</td>
                                                <td>{{ $v->full_name }}</td>
                                                <td>{{ $v->student_no }}</td>
                                                <td>{{ $v->student_email }}</td>
                                                <td>{{ $v->date_reported }}</td>
                                                <td>{{ $v->offense }}</td>
                                                <td>{{ $v->level }}</td>
                                                <td style="color: {{ $v->status === 'Complete' ? 'blue' : 'red' }}">{{ $v->status }}
                                                </td>
                                                <td>{{ $v->action_taken ?? 'N/A' }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary edit-btn" data-id="{{ $v->id }}"
                                                        data-violation-no="{{ $v->violation_no }}"
                                                        data-full-name="{{ $v->full_name }}" data-student-no="{{ $v->student_no }}"
                                                        data-student-email="{{ $v->student_email }}"
                                                        data-date-reported="{{ $v->date_reported }}"
                                                        data-yearlvl-degree="{{ $v->yearlvl_degree }}"
                                                        data-offense="{{ $v->offense }}" data-level="{{ $v->level }}"
                                                        data-status="{{ $v->status }}" data-action-taken="{{ $v->action_taken }}"
                                                        data-bs-toggle="modal" data-bs-target="#editViolationModal">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>

                                                    <form action="{{ route($prefix . 'violations.destroy', $v->id) }}" method="POST"
                                                        style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger"><i
                                                                class="fas fa-trash-alt"></i></button>
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

    @include('violation-modals') {{-- Optional if you want to move modals to a separate file --}}

    {{-- Select2 & Modal Script --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                dropdownParent: $('#addViolationModal')
            });
            $('#editOffense').select2({
                dropdownParent: $('#editViolationModal')
            });

            $('.edit-btn').on('click', function () {
                $('#editId').val($(this).data('id'));
                $('#editViolationNo').val($(this).data('violationNo'));
                $('#editFullName').val($(this).data('fullName'));
                $('#editStudentNo').val($(this).data('studentNo'));
                $('#editStudentEmail').val($(this).data('studentEmail'));
                $('#editDateReported').val($(this).data('dateReported'));
                $('#editYearlvlDegree').val($(this).data('yearlvlDegree'));
                $('#editOffense').val($(this).data('offense')).trigger('change');
                $('#editLevel').val($(this).data('level'));
                $('#editStatus').val($(this).data('status'));
                $('#editActionTaken').val($(this).data('actionTaken'));
            });

            $('#editViolationForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editId').val();
                const action = "{{ route('admin.violations.update', ':id') }}".replace(':id', id);
                $(this).attr('action', action);
                this.submit();
            });


        });
    </script>

@endsection