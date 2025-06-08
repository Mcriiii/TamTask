@extends("layout.main")
@section("title", "Complaints")
@section("content")
@php
    $prefix = Auth::user()->role == 'admin' ? 'admin.' : '';
@endphp
    <!-- Top Navbar -->
    <div class="top-navbar">
        <img src="{{ asset('images/logoo.png') }}" alt="" class="logo-nav">
        <div class="user-greeting">Hello, Admin</div>
    </div>

    <!-- Sidebar + Content -->
    <div class="main-wrapper">
         @include('layout.sidebar')
        <div class="main-content">
            <div class="container mt-5">
                <div class="card shadow rounded">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Complaint Reports</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route($prefix .'complaints.index') }}" class="row g-3 mb-4">
                            <div class="col-md-3">
                                <input type="text" name="student_no" value="{{ request('student_no') }}"
                                    class="form-control" placeholder="Filter by Student No">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date_reported" value="{{ request('date_reported') }}"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="yearlvl_degree" value="{{ request('yearlvl_degree') }}"
                                    class="form-control" placeholder="Filter by Year & Degree">
                            </div>
                            <div class="col-md-3">
                                <select name="subject" class="form-select">
                                    <option value="">Select subject</option>
                                    <option value="Bullying" {{ request('subject') == 'Bullying' ? 'selected' : '' }}>Bullying
                                    </option>
                                    <option value="Harassment" {{ request('subject') == 'Harassment' ? 'selected' : '' }}>
                                        Harassment</option>
                                    <option value="Discrimination" {{ request('subject') == 'Discrimination' ? 'selected' : '' }}>Discrimination</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="{{ route($prefix .'complaints.index') }}" class="btn btn-secondary me-2">Clear</a>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#addComplaintModal">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </form>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($complaints->isEmpty())
                            <p>No complaints found.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Ticket No</th>
                                            <th>Reporter</th>
                                            <th>Student No</th>
                                            <th>Date</th>
                                            <th>Year & Degree</th>
                                            <th>Subject</th>
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
                                                <td>
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                        data-bs-target="#editComplaintModal" data-id="{{ $complaint->id }}"
                                                        data-reporter="{{ $complaint->reporter_name }}"
                                                        data-student="{{ $complaint->student_no }}"
                                                        data-date="{{ $complaint->date_reported }}"
                                                        data-degree="{{ $complaint->yearlvl_degree }}"
                                                        data-subject="{{ $complaint->subject }}">
                                                        Edit
                                                    </button>
                                                    <form action="{{ route($prefix .'complaints.destroy', $complaint->id) }}" method="POST"
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
                </div>
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
                            <form action="{{ route($prefix .'complaints.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Ticket No.</label>
                                    <input type="text" name="ticket_no" class="form-control" value="{{ $ticketNo }}"
                                        readonly>
                                </div>
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
                                    <input type="text" name="yearlvl_degree" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Subject</label>
                                    <select name="subject" class="form-select" required>
                                        <option value="Bullying">Bullying</option>
                                        <option value="Harassment">Harassment</option>
                                        <option value="Discrimination">Discrimination</option>
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
                                    <input type="text" name="yearlvl_degree" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Subject</label>
                                    <select name="subject" class="form-select" required>
                                        <option value="Bullying">Bullying</option>
                                        <option value="Harassment">Harassment</option>
                                        <option value="Discrimination">Discrimination</option>
                                    </select>
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
        document.addEventListener('DOMContentLoaded', function () {
            const editModal = document.getElementById('editComplaintModal');
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const reporter = button.getAttribute('data-reporter');
                const student = button.getAttribute('data-student');
                const date = button.getAttribute('data-date');
                const degree = button.getAttribute('data-degree');
                const subject = button.getAttribute('data-subject');

                const form = editModal.querySelector('#editForm');
                form.action = `/complaints/${id}`;
                form.querySelector('[name="reporter_name"]').value = reporter;
                form.querySelector('[name="student_no"]').value = student;
                form.querySelector('[name="date_reported"]').value = date;
                form.querySelector('[name="yearlvl_degree"]').value = degree;
                form.querySelector('[name="subject"]').value = subject;
            });
        });
    </script>
@endsection