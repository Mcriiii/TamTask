@extends("layout.main")
@section("title", "Lost and Found")
@section("content")
@php
$prefix = Auth::user()->role == 'admin' ? 'admin.' : '';
@endphp
<style>
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
        /* spacing between rows */
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

    /* Apply pill shape to entire row */
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

    .custom-pdf-btn {
        background-color: #ff4d4f;
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        font-weight: bold;
        font-size: 0.95rem;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(255, 77, 79, 0.3);
        transition: all 0.3s ease;
    }

    .custom-pdf-btn:hover {
        background-color: #e04345;
        box-shadow: 0 6px 14px rgba(255, 77, 79, 0.4);
        transform: translateY(-1px);
    }

    .custom-pdf-btn i {
        margin-right: 6px;
    }
</style>


<div class="top-navbar">
    <img src="{{ asset('images/logoo.png') }}" alt="" class="logo-nav">
    <div class="user-greeting">
        @auth
        Hello, {{ Auth::user()->first_name }}
        @else
        Hello, Guest
        @endauth
    </div>
</div>

<div class="main-wrapper">
    @include('layout.sidebar')

    <div class="main-content">
        <div class="card-header text-black">
            <h4 class="mb-0">Lost and Found Reports</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            <form method="GET" action="{{ route($prefix . 'lost-found.export.pdf') }}" class="row g-2 align-items-end mb-4">
                <div class="col-md-3">
                    <label for="month">Export by Month</label>
                    <input type="month" name="month" id="month" class="form-control" value="{{ request('month') }}">
                </div>
                <div class="col-md-2">
                    <label for="year">Or Year</label>
                    <input type="number" name="year" id="year" class="form-control" min="2020" value="{{ request('year') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn custom-pdf-btn">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </form>

            <form method="GET" action="{{ route($prefix . 'lost-found.index') }}" class="row g-3 mb-4">
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
                        <a href="{{ route($prefix . 'lost-found.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#chooseReportTypeModal">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
            </form>

            @if($reports->isEmpty())
            <p>No reports found.</p>
            @else
            <div class="modern-table-container">
                <table class="modern-table">

                    <thead>
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
                                    data-ticket="{{ $report->ticket_no }}"
                                    data-reporter="{{ $report->reporter_name }}"
                                    data-email="{{ $report->email }}"
                                    data-date="{{ $report->date_reported }}"
                                    data-location="{{ $report->location_found }}"
                                    data-type="{{ $report->item_type }}"
                                    data-description="{{ $report->description }}"
                                    data-status="{{ $report->status }}">
                                    Edit
                                </button>

                                @if(in_array($report->status, ['Unclaimed', 'Item Stored', 'Found', 'Searching']))
                                <form action="{{ route($prefix . 'lost-found.claim', $report->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Mark Claimed</button>
                                </form>
                                @endif

                                <form action="{{ route($prefix . 'lost-found.destroy', $report->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure to delete this report?');">
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

            <!-- Choose Report Type Modal -->
            <div class="modal fade" id="chooseReportTypeModal" tabindex="-1">
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

            <!-- Add Modal -->
            <div class="modal fade" id="addLostFoundModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Lost and Found Report</h5>
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
                            <form action="{{ route($prefix . 'lost-found.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="_modal" value="add">
                                <input type="hidden" name="report_type" id="reportType">
                                <div class="mb-3"><label>Your Name</label><input type="text" name="reporter_name" class="form-control" value="{{ old('reporter_name') }}" required></div>
                                <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}"></div>
                                <div class="mb-3"><label>Date Reported</label><input type="date" name="date_reported" class="form-control" value="{{ old('date_reported') }}" required></div>
                                <div class="mb-3" id="locationField"><label>Location Found</label><input type="text" name="location_found" class="form-control" value="{{ old('location_found') }}"></div>
                                <div class="mb-3"><label>Item Type</label>
                                    <select name="item_type" class="form-select" required>
                                        <option value="">Select</option>
                                        <option value="Wallet">Wallet</option>
                                        <option value="Phone">Phone</option>
                                        <option value="Bag">Bag</option>
                                        <option value="Keys">Keys</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>
                                <div class="mb-3"><label>Description</label><textarea name="description" class="form-control">{{ old('description') }}</textarea></div>
                                <div class="text-end"><button type="submit" class="btn btn-primary">Submit</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Edit Modal -->
            <div class="modal fade" id="editLostFoundModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Lost and Found Report</h5>
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
                            <form method="POST" id="editForm"
                                action="{{ old('_modal') === 'edit' && session('edit_id') ? route($prefix . 'lost-found.update', session('edit_id')) : '' }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_modal" value="edit">
                                <div class="mb-3">
                                    <label>Reporter Name</label>
                                    <input type="text" name="reporter_name" class="form-control"
                                        value="{{ old('_modal') === 'edit' ? old('reporter_name') : '' }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('_modal') === 'edit' ? old('email') : '' }}">
                                </div>
                                <div class="mb-3">
                                    <label>Date Reported</label>
                                    <input type="date" name="date_reported" class="form-control"
                                        value="{{ old('_modal') === 'edit' ? old('date_reported') : '' }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Location Found</label>
                                    <input type="text" name="location_found" class="form-control"
                                        value="{{ old('_modal') === 'edit' ? old('location_found') : '' }}">
                                </div>
                                <div class="mb-3">
                                    <label>Item Type</label>
                                    <select name="item_type" class="form-select" required>
                                        <option value="Wallet" {{ old('_modal') === 'edit' && old('item_type') === 'Wallet' ? 'selected' : '' }}>Wallet</option>
                                        <option value="Phone" {{ old('_modal') === 'edit' && old('item_type') === 'Phone' ? 'selected' : '' }}>Phone</option>
                                        <option value="Bag" {{ old('_modal') === 'edit' && old('item_type') === 'Bag' ? 'selected' : '' }}>Bag</option>
                                        <option value="Keys" {{ old('_modal') === 'edit' && old('item_type') === 'Keys' ? 'selected' : '' }}>Keys</option>
                                        <option value="Others" {{ old('_modal') === 'edit' && old('item_type') === 'Others' ? 'selected' : '' }}>Others</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control">{{ old('_modal') === 'edit' ? old('description') : '' }}</textarea>
                                </div>
                                @php
                                use Illuminate\Support\Str;
                                $isFound = Str::startsWith($report->ticket_no, 'FND');
                                @endphp

                                <div class="mb-3">
                                    <label>Status</label>
                                    <select name="status" class="form-select" id="edit-status-dropdown" required></select>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Update Report</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>



        </div>


    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const alert = document.getElementById('success-alert');
            if (alert) {
                // Bootstrap fade out
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500); // remove after fade out
            }
        }, 3000);

        const modalError = document.getElementById('modalError')?.value;

        if (modalError === 'add') {
            new bootstrap.Modal(document.getElementById('addLostFoundModal')).show();
        }

        if (modalError === 'edit') {
            new bootstrap.Modal(document.getElementById('editLostFoundModal')).show();
        }

        const editModal = document.getElementById('editLostFoundModal');

        editModal.addEventListener('show.bs.modal', function(event) {
            // Prevent override if modalError is 'edit' (validation error)
            if (modalError === 'edit') return;

            const button = event.relatedTarget;
            const form = editModal.querySelector('#editForm');

            const statusSelect = form.querySelector('#edit-status-dropdown');
            const currentStatus = button.getAttribute('data-status') || '';
            const ticketNo = button.getAttribute('data-ticket') || '';
            const isFound = ticketNo.startsWith('FND');

            // Define options
            const foundOptions = ['Item Stored', 'Claimed', 'Disposed'];
            const lostOptions = ['Searching', 'Found', 'Claimed', 'Closed'];
            const options = isFound ? foundOptions : lostOptions;

            form.action = `{{ route($prefix . 'lost-found.update', ['id' => '__id']) }}`.replace('__id', button.getAttribute('data-id'));
            form.querySelector('[name="reporter_name"]').value = button.getAttribute('data-reporter');
            form.querySelector('[name="email"]').value = button.getAttribute('data-email');
            form.querySelector('[name="date_reported"]').value = button.getAttribute('data-date');
            form.querySelector('[name="location_found"]').value = button.getAttribute('data-location');
            form.querySelector('[name="item_type"]').value = button.getAttribute('data-type');
            form.querySelector('[name="description"]').value = button.getAttribute('data-description');

            // Reset and fill dropdown
            statusSelect.innerHTML = '';
            options.forEach(option => {
                const opt = document.createElement('option');
                opt.value = option;
                opt.text = option;
                if (option === currentStatus) opt.selected = true;
                statusSelect.appendChild(opt);
            });
        });
    });

    function openAddModal(prefix) {
        // Set hidden input
        const typeInput = document.getElementById('reportType');
        if (typeInput) {
            typeInput.value = prefix;
        }

        // Show or hide Location field (only for Found items)
        const locationField = document.getElementById('locationField');
        if (locationField) {
            locationField.style.display = prefix === 'LOS' ? 'none' : 'block';
        }

        // Hide the "Choose Report Type" modal if open
        const chooseModalEl = document.getElementById('chooseReportTypeModal');
        const chooseModal = bootstrap.Modal.getInstance(chooseModalEl);
        if (chooseModal) {
            chooseModal.hide();
        }

        // Show the Add Report modal
        const addModal = new bootstrap.Modal(document.getElementById('addLostFoundModal'));
        addModal.show();
    }
</script>
<input type="hidden" id="modalError" value="{{ old('_modal') }}">

@endsection