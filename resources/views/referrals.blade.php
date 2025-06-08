@extends("layout.main")
@section("title", "Referral Reports")
@section("content")

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
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Referral Reports</h4>
                        <button type="button" class="btn btn-light text-success" data-bs-toggle="modal"
                            data-bs-target="#addReferralModal">
                            <i class="fas fa-plus"></i> Add Referral
                        </button>
                    </div>
                    <div class="card-body">
                        {{-- Filter (Only Date) --}}
                        <form method="GET" action="{{ route('referrals.index') }}" class="row g-3 mb-4">
                            <div class="col-md-6">
                                <input type="datetime-local" name="date" class="form-control" value="{{ request('date') }}">
                            </div>
                            <div class="col-md-6 d-flex justify-content-between">
                                <div>
                                    <button type="submit" class="btn btn-success me-1">Filter</button>
                                    <a href="{{ route('referrals.index') }}" class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </form>

                        {{-- Success Message --}}
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        {{-- Referral Table --}}
                        @if($referrals->isEmpty())
                            <p>No referral reports found.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Referral No</th>
                                            <th>Date Reported</th>
                                            <th>Level</th>
                                            <th>Date to See</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($referrals as $referral)
                                            <tr>
                                                <td>{{ $referral->referral_no }}</td>
                                                <td>{{ \Carbon\Carbon::parse($referral->date_reported)->format('m/d/y h:i a') }}
                                                </td>
                                                <td>{{ $referral->level }}</td>
                                                <td>{{ \Carbon\Carbon::parse($referral->date_to_see)->format('m/d/y h:i a') }}</td>
                                                <td>{{ $referral->role }}</td>
                                                <td style="color: {{ $referral->status === 'Complete' ? 'blue' : 'red' }}">
                                                    {{ $referral->status }}
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="editReferral({{ $referral }})"
                                                        data-bs-toggle="modal" data-bs-target="#editReferralModal">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <form action="{{ route('referrals.destroy', $referral->id) }}" method="POST"
                                                        class="d-inline" onsubmit="return confirm('Delete this referral?');">
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
                            {{ $referrals->withQueryString()->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Referral Modal -->
    <div class="modal fade" id="addReferralModal" tabindex="-1" aria-labelledby="addReferralModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('referrals.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addReferralModalLabel">Add Referral</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Referral No.</label>
                        <input type="text" name="referral_no" class="form-control"
                            value="{{ 'REF-' . strtoupper(uniqid()) }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Reported</label>
                        <input type="datetime-local" name="date_reported" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select name="level" class="form-select" required>
                            <option value="Level 1">Level 1 - Less Serious</option>
                            <option value="Level 2">Level 2 - Moderately Serious</option>
                            <option value="Level 3">Level 3 - Very Serious</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date to See</label>
                        <input type="datetime-local" name="date_to_see" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="Student">Student</option>
                            <option value="Teacher">Teacher</option>
                            <option value="Associate">Associate</option>
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
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Referral Modal (Single) -->
    <div class="modal fade" id="editReferralModal" tabindex="-1" aria-labelledby="editReferralModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('referrals.update', $referral->id) }}" id="editReferralForm" method="POST"
                class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editReferralModalLabel">Edit Referral</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="referral_id" id="editReferralId">
                    <div class="mb-3">
                        <label class="form-label">Referral No.</label>
                        <input type="text" class="form-control" id="editReferralNo" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Reported</label>
                        <input type="datetime-local" name="date_reported" id="editDateReported" class="form-control"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select name="level" id="editLevel" class="form-select" required>
                            <option value="Level 1">Level 1</option>
                            <option value="Level 2">Level 2</option>
                            <option value="Level 3">Level 3</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date to See</label>
                        <input type="datetime-local" name="date_to_see" id="editDateToSee" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="editRole" class="form-select" required>
                            <option value="Student">Student</option>
                            <option value="Teacher">Teacher</option>
                            <option value="Associate">Associate</option>
                            <option value="Security">Security</option>
                            <option value="SFU">SFU</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="editStatus" class="form-select" required>
                            <option value="Pending">Pending</option>
                            <option value="Complete">Complete</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for Dynamic Edit Modal -->
    <script>
        const baseUpdateUrl = "{{ url('referrals') }}";

        function editReferral(referral) {
            const form = document.getElementById('editReferralForm');
            form.action = `${baseUpdateUrl}/${referral.id}`; // dynamic action URL

            document.getElementById('editReferralId').value = referral.id;
            document.getElementById('editReferralNo').value = referral.referral_no;
            document.getElementById('editDateReported').value = formatDate(referral.date_reported);
            document.getElementById('editLevel').value = referral.level;
            document.getElementById('editDateToSee').value = formatDate(referral.date_to_see);
            document.getElementById('editRole').value = referral.role;
            document.getElementById('editStatus').value = referral.status;
        }

        function formatDate(datetime) {
            const date = new Date(datetime);
            const offset = date.getTimezoneOffset();
            const localDate = new Date(date.getTime() - (offset * 60 * 1000));
            return localDate.toISOString().slice(0, 16); // Format 'YYYY-MM-DDTHH:MM'
        }
    </script>


@endsection