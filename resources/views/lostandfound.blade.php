
@extends("layout.main")
@section("title", "Lost and Found")
@section("content")

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
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Lost and Found Reports</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('lost-found.index') }}" class="row g-3 mb-4">
                        <div class="col-md-5">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name, ticket, or description">
                        </div>
                        <div class="col-md-4">
                            <select name="item_type" class="form-select">
                                <option value="">-- Filter by Item --</option>
                                @foreach ($itemTypes as $item)
                                <option value="{{ $item }}" {{ request('item_type') == $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary me-1">Filter</button>
                                <a href="{{ route('lost-found.index') }}" class="btn btn-secondary">Clear</a>
                            </div>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#chooseReportTypeModal">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </form>

                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($reports->isEmpty())
                    <p>No reports found.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ticket No</th>
                                    <th>Reporter</th>
                                    <th>Email</th>
                                    <th>Date</th>
                                    <th>Location Found</th>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $report)
                                <tr>
                                    <td>{{ $report->ticket_no }}</td>
                                    <td>{{ $report->reporter_name }}</td>
                                    <td>{{ $report->email ?? '-' }}</td>
                                    <td>{{ $report->date_reported }}</td>
                                    <td>{{ $report->location_found ?? 'N/A' }}</td>
                                    <td>{{ $report->item_type }}</td>
                                    <td>{{ $report->description }}</td>
                                    <td>{{ $report->status }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editLostFoundModal"
                                            data-id="{{ $report->id }}"
                                            data-reporter="{{ $report->reporter_name }}"
                                            data-email="{{ $report->email }}"
                                            data-date="{{ $report->date_reported }}"
                                            data-location="{{ $report->location_found }}"
                                            data-type="{{ $report->item_type }}"
                                            data-description="{{ $report->description }}"
                                            data-status="{{ $report->status }}">
                                            Edit
                                        </button>

                                        @if($report->status === 'Unclaimed')
                                        <form action="{{ route('lost-found.claim', $report->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Mark Claimed</button>
                                        </form>
                                        @endif

                                        <form action="{{ route('lost-found.destroy', $report->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure to delete this report?');">
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
                    {{ $reports->withQueryString()->links() }}
                    @endif

                    <!-- Modals and JS are the same as before with ticket_no removed in Add Modal -->

<!-- Choose Report Type Modal -->
<div class="modal fade" id="chooseReportTypeModal" tabindex="-1" aria-labelledby="chooseReportTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Choose Report Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <button type="button" class="btn btn-outline-primary me-2" onclick="openAddModal('FND')">Found Item</button>
                <button type="button" class="btn btn-outline-warning" onclick="openAddModal('LOS')">Lost Item</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Report Modal -->
<div class="modal fade" id="addLostFoundModal" tabindex="-1" aria-labelledby="addLostFoundModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Lost and Found Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('lost-found.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="report_type" id="reportType">
                    <div class="mb-3">
                        <label class="form-label">Your Name</label>
                        <input type="text" name="reporter_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Reported</label>
                        <input type="date" name="date_reported" class="form-control" required>
                    </div>
                    <div class="mb-3" id="locationField">
                        <label class="form-label">Location Found</label>
                        <input type="text" name="location_found" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Item Type</label>
                        <select name="item_type" class="form-select" required>
                            <option value="Wallet">Wallet</option>
                            <option value="Phone">Phone</option>
                            <option value="Bag">Bag</option>
                            <option value="Keys">Keys</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editLostFoundModal" tabindex="-1" aria-labelledby="editLostFoundModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Lost and Found Report</h5>
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
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Reported</label>
                        <input type="date" name="date_reported" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location Found</label>
                        <input type="text" name="location_found" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Item Type</label>
                        <select name="item_type" class="form-select" required>
                            <option value="Wallet">Wallet</option>
                            <option value="Phone">Phone</option>
                            <option value="Bag">Bag</option>
                            <option value="Keys">Keys</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Unclaimed">Unclaimed</option>
                            <option value="Claimed">Claimed</option>
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Update Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</div> <!-- main-content -->

<script>
function openAddModal(prefix) {
    document.getElementById('reportType').value = prefix;
    document.getElementById('locationField').style.display = prefix === 'LOS' ? 'none' : 'block';
    bootstrap.Modal.getInstance(document.getElementById('chooseReportTypeModal')).hide();
    new bootstrap.Modal(document.getElementById('addLostFoundModal')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editLostFoundModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const form = editModal.querySelector('#editForm');
        form.action = `/lost-found/${button.getAttribute('data-id')}`;
        form.querySelector('[name="reporter_name"]').value = button.getAttribute('data-reporter');
        form.querySelector('[name="email"]').value = button.getAttribute('data-email');
        form.querySelector('[name="date_reported"]').value = button.getAttribute('data-date');
        form.querySelector('[name="location_found"]').value = button.getAttribute('data-location');
        form.querySelector('[name="item_type"]').value = button.getAttribute('data-type');
        form.querySelector('[name="description"]').value = button.getAttribute('data-description');
        form.querySelector('[name="status"]').value = button.getAttribute('data-status');
    });
});
</script>
@endsection
