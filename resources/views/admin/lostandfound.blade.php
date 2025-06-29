@extends("layout.main")
@section("title", "Lost and Found")
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

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
        /* spacing between rows */
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
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="text-button fw-bold" style="color: #006E44; padding-left: 10px; font-size: 1.05rem;">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </button>
                        </div>
                    </form>

                    <form method="GET" action="{{ route($prefix . 'lost-found.index') }}" class="row g-3 mb-4">
                        <!-- Search Input -->
                        <div class="col-md-3">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name, ticket, description, or email">
                        </div>

                        <!-- Item Type Filter -->
                        <div class="col-md-3">
                            <select name="item_type" class="form-select">
                                <option value="">-- Filter by Item --</option>
                                @foreach ($itemTypes as $item)
                                <option value="{{ $item }}" {{ request('item_type') == $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- ✅ Status Filter Dropdown -->
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">-- Filter by Status --</option>
                                @php
                                $statuses = ['Unclaimed', 'Found', 'Item Stored', 'Claimed', 'Disposed', 'Searching', 'Closed'];
                                @endphp
                                @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="col-md-3 d-flex align-items-end gap-3 justify-content-between">
                            <div>
                                <button type="submit" class="link-button">Filter</button>
                                <a href="{{ route($prefix . 'lost-found.index') }}" class="link-button">Clear</a>
                            </div>
                            <button type="button" class="btn text-dark fw-bold" style="background-color: #FFD100; border: none;" data-bs-toggle="modal" data-bs-target="#chooseReportTypeModal">
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
                                @php
                                $isCustom = !$itemTypes->contains($report->item_type);
                                @endphp
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
                                        <button
                                            class="text-button text-primary me-3"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editLostFoundModal"
                                            data-id="{{ $report->id }}"
                                            data-ticket="{{ $report->ticket_no }}"
                                            data-reporter="{{ $report->reporter_name }}"
                                            data-email="{{ $report->email }}"
                                            data-date="{{ $report->date_reported }}"
                                            data-location="{{ $report->location_found }}"
                                            data-type="{{ in_array($report->item_type, ['Wallet', 'Phone', 'Bag', 'Keys']) ? $report->item_type : 'Others' }}"
                                            data-custom-type="{{ in_array($report->item_type, ['Wallet', 'Phone', 'Bag', 'Keys']) ? '' : $report->item_type }}"
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

                                        <form action="{{ route($prefix . 'lost-found.destroy', $report->id) }}"
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
                                            <select name="item_type" id="itemTypeSelect" class="form-select" required>
                                                <option value="">Select</option>
                                                <option value="Wallet" {{ old('item_type') == 'Wallet' ? 'selected' : '' }}>Wallet</option>
                                                <option value="Phone" {{ old('item_type') == 'Phone' ? 'selected' : '' }}>Phone</option>
                                                <option value="Bag" {{ old('item_type') == 'Bag' ? 'selected' : '' }}>Bag</option>
                                                <option value="Keys" {{ old('item_type') == 'Keys' ? 'selected' : '' }}>Keys</option>
                                                <option value="Others"
                                                    {{ old('_modal') === 'edit' && (old('item_type') == 'Others' || old('custom_item_type')) ? 'selected' : '' }}>
                                                    Others
                                                </option>
                                            </select>
                                        </div>
                                        <div class="mb-3" id="customItemTypeGroup" style="display:none;">
                                            <label>Specify Item Type</label>
                                            <input type="text" name="custom_item_type" id="customItemType" class="form-control"
                                                value="{{ old('custom_item_type') }}">
                                        </div>
                                        <div class="mb-3"><label>Description</label><textarea name="description" class="form-control" placeholder="Provide a brief description of the item">{{ old('description') }}</textarea></div>
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
                                            <select name="item_type" id="editItemTypeSelect" class="form-select" required>
                                                <option value="">Select</option>
                                                <option value="Wallet">Wallet</option>
                                                <option value="Phone">Phone</option>
                                                <option value="Bag">Bag</option>
                                                <option value="Keys">Keys</option>
                                                <option value="Others">Others</option>
                                            </select>
                                        </div>

                                        <!-- FIXED: Show custom item field if needed -->
                                        <div class="mb-3" id="editCustomItemTypeGroup"
                                            style="{{ old('_modal') === 'edit' && old('item_type') === 'Others' ? '' : 'display:none;' }}">
                                            <label>Specify Item Type</label>
                                            <input type="text" name="custom_item_type" id="editCustomItemType" class="form-control"
                                                value="{{ old('_modal') === 'edit' ? old('custom_item_type') : '' }}">
                                        </div>

                                        <div class="mb-3">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control">{{ old('_modal') === 'edit' ? old('description') : '' }}</textarea>
                                        </div>

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
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalError = document.getElementById('modalError')?.value;
        const oldCustomValue = document.getElementById('oldCustomItemType')?.value || '';

        // Auto-reopen modals on validation error
        if (modalError === 'add') {
            new bootstrap.Modal(document.getElementById('addLostFoundModal')).show();
        }

        if (modalError === 'edit') {
            new bootstrap.Modal(document.getElementById('editLostFoundModal')).show();
        }

        // Fade out success alert
        setTimeout(() => {
            const alert = document.getElementById('success-alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);

        // Toggle custom input logic
        function toggleCustomItemField(select, group, inputId) {
            const input = document.getElementById(inputId);
            if (select.value === 'Others') {
                group.style.display = 'block';
            } else {
                group.style.display = 'none';
                if (input) input.value = '';
            }
        }

        // Add Modal: toggle custom item field
        const itemTypeSelect = document.getElementById('itemTypeSelect');
        const customItemGroup = document.getElementById('customItemTypeGroup');
        if (itemTypeSelect && customItemGroup) {
            toggleCustomItemField(itemTypeSelect, customItemGroup, 'customItemType');
            itemTypeSelect.addEventListener('change', function() {
                toggleCustomItemField(this, customItemGroup, 'customItemType');
            });
        }

        // Open Add Modal and adjust location field
        window.openAddModal = function(prefix) {
            document.getElementById('reportType').value = prefix;

            const locationField = document.getElementById('locationField');
            locationField.style.display = prefix === 'LOS' ? 'none' : 'block';

            bootstrap.Modal.getInstance(document.getElementById('chooseReportTypeModal')).hide();
            new bootstrap.Modal(document.getElementById('addLostFoundModal')).show();
        };

        // Edit Modal logic
        const editModal = document.getElementById('editLostFoundModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            if (modalError === 'edit') return;

            const button = event.relatedTarget;
            const form = editModal.querySelector('#editForm');

            // Get values from data attributes
            const ticketNo = button.getAttribute('data-ticket') || '';
            const isFound = ticketNo.startsWith('FND');

            const reporter = button.getAttribute('data-reporter');
            const email = button.getAttribute('data-email');
            const date = button.getAttribute('data-date');
            const location = button.getAttribute('data-location');
            const itemType = button.getAttribute('data-type');
            const customType = button.getAttribute('data-custom-type') || '';
            const description = button.getAttribute('data-description');
            const status = button.getAttribute('data-status');

            // Populate form fields
            form.action = `{{ route($prefix . 'lost-found.update', ['id' => '__id']) }}`.replace('__id', button.getAttribute('data-id'));
            form.querySelector('[name="reporter_name"]').value = reporter;
            form.querySelector('[name="email"]').value = email;
            form.querySelector('[name="date_reported"]').value = date;
            form.querySelector('[name="location_found"]').value = location;
            form.querySelector('[name="description"]').value = description;

            // Location field visibility
            const locationFieldGroup = form.querySelector('[name="location_found"]').closest('.mb-3');
            locationFieldGroup.style.display = ticketNo.startsWith('LOS') ? 'none' : 'block';

            // Set item type
            const itemTypeDropdown = form.querySelector('#editItemTypeSelect');
            itemTypeDropdown.value = itemType;

            // Handle custom item field
            let customGroup = document.getElementById('editCustomItemTypeGroup');
            let customInput = document.getElementById('editCustomItemType');

            customGroup = document.getElementById('editCustomItemTypeGroup');
            customInput = document.getElementById('editCustomItemType');

            if (!customGroup || !customInput) {
                const newGroup = document.createElement('div');
                newGroup.classList.add('mb-3');
                newGroup.id = 'editCustomItemTypeGroup';
                newGroup.innerHTML = `
        <label for="editCustomItemType">Specify Item Type</label>
        <input type="text" name="custom_item_type" id="editCustomItemType" class="form-control" value="">
    `;
                const after = itemTypeDropdown.closest('.mb-3');
                after.insertAdjacentElement('afterend', newGroup);

                customGroup = newGroup;
                customInput = newGroup.querySelector('input');
            }

            setTimeout(() => {
                if (itemType === 'Others') {
                    customGroup.style.display = 'block';
                    customInput.value = customType;
                } else {
                    customGroup.style.display = 'none';
                    customInput.value = '';
                }
            }, 50);

            // Dropdown change handler for "Others"
            itemTypeDropdown.addEventListener('change', function() {
                toggleCustomItemField(this, customGroup, 'editCustomItemType');
            });

            // Status dropdown
            const statusSelect = form.querySelector('#edit-status-dropdown');
            const foundOptions = ['Item Stored', 'Claimed', 'Disposed'];
            const lostOptions = ['Searching', 'Found', 'Claimed', 'Closed'];
            const options = isFound ? foundOptions : lostOptions;

            statusSelect.innerHTML = '';
            options.forEach(option => {
                const opt = document.createElement('option');
                opt.value = option;
                opt.text = option;
                if (option === status) opt.selected = true;
                statusSelect.appendChild(opt);
            });
        });

        // SweetAlert confirmation for delete
        window.confirmDelete = function(form) {
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
        };
    });
</script>



<input type="hidden" id="modalError" value="{{ old('_modal') }}">
<input type="hidden" id="oldCustomItemType" value="{{ old('custom_item_type') }}">

@endsection