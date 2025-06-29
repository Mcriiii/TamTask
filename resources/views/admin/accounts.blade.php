@extends("layout.main")
@section("title", "Account")
@section("content")
@php
$prefix = Auth::user()->role === 'admin' ? 'admin.' : '';
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

  .modern-table .btn-info {
    background-color: #00b4d8;
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

        <h2 class="mb-0">Manage Accounts</h2>

        <!-- Add Account Button -->
        <div class="d-flex justify-content-end mb-3">
          <button type="button" class="btn text-dark fw-bold" style="background-color: #FFD100; border: none;" data-bs-toggle="modal" data-bs-target="#addAccountModal">
            Add Account
          </button>
        </div>

        <!-- Accounts Table -->
        <table class="modern-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
            <tr>
              <td>{{ $user->first_name }} {{ $user->last_name }}</td>
              <td>{{ $user->email }}</td>
              <td>{{ $user->role }}</td>
              <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
              <td>
                <button class="text-button text-primary me-3 editBtn"
                  data-id="{{ $user->id }}"
                  data-first-name="{{ $user->first_name }}"
                  data-last-name="{{ $user->last_name }}"
                  data-role="{{ $user->role }}"
                  data-email="{{ $user->email }}"
                  data-bs-toggle="modal" data-bs-target="#editAccountModal">
                  Edit
                </button>
                <form action="{{ route($prefix . 'accounts.destroy', $user->id) }}"
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


        <!-- Add Account Modal -->
        <div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form method="POST" action="{{ route($prefix . 'accounts.store') }}">
              @csrf
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="addAccountModalLabel">Add Account</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <!-- Add Account Error Block -->
                  @if ($errors->any())
                  <div class="alert alert-danger">
                    <ul>
                      @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                  @endif

                  <input type="text" name="first_name" class="form-control mb-2" placeholder="First Name" value="{{ old('first_name') }}" required>
                  <input type="text" name="last_name" class="form-control mb-2" placeholder="Last Name" value="{{ old('last_name') }}" required>
                  <input type="email" name="email" class="form-control mb-2" placeholder="Email" value="{{ old('email') }}" required>
                  <select name="role" class="form-control mb-2" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                    <option value="security" {{ old('role') == 'security' ? 'selected' : '' }}>Security</option>
                    <option value="sfu" {{ old('role') == 'sfu' ? 'selected' : '' }}>SFU</option>
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                  </select>
                  <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
                  <input type="password" name="password_confirmation" class="form-control mb-2" placeholder="Confirm Password" required>

                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-success">Add</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Edit Account Modal -->
        <!-- Edit Account Modal -->
        <div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form method="POST" id="editAccountForm"
              action="{{ old('_modal') === 'edit' && old('id') ? url($prefix . 'accounts/' . old('id')) : '' }}">
              @csrf
              @method('PUT')
              <input type="hidden" name="_modal" value="edit">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editAccountModalLabel">Edit Account</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  @if ($errors->editAccount->any())
                  <div class="alert alert-danger">
                    <ul>
                      @foreach ($errors->editAccount->all() as $error)
                      <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                  @endif

                  <input type="hidden" id="editUserId" name="id" value="{{ old('_modal') === 'edit' ? old('id') : '' }}">
                  <input type="text" name="first_name" id="editFirstName" class="form-control mb-2"
                    value="{{ old('_modal') === 'edit' ? old('first_name') : '' }}" placeholder="First Name" required>
                  <input type="text" name="last_name" id="editLastName" class="form-control mb-2"
                    value="{{ old('_modal') === 'edit' ? old('last_name') : '' }}" placeholder="Last Name" required>
                  <input type="email" name="email" id="editEmail" class="form-control mb-2"
                    value="{{ old('_modal') === 'edit' ? old('email') : '' }}" placeholder="Email" required>

                  <input type="password" name="password" class="form-control mb-2" placeholder="New Password (leave blank if unchanged)">
                  <input type="password" name="password_confirmation" class="form-control mb-2" placeholder="Confirm New Password">
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Edit Button Script -->
<script>
   document.querySelectorAll('.editBtn').forEach(button => {
    button.addEventListener('click', function () {
      const userId = this.getAttribute('data-id');
      const firstName = this.getAttribute('data-first-name');
      const lastName = this.getAttribute('data-last-name');
      const userEmail = this.getAttribute('data-email');
      const form = document.getElementById('editAccountForm');

      document.getElementById('editUserId').value = userId;
      document.getElementById('editFirstName').value = firstName;
      document.getElementById('editLastName').value = lastName;
      document.getElementById('editEmail').value = userEmail;

      form.action = `{{ route($prefix . 'accounts') }}/${userId}`;
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

<!-- Auto Open Correct Modal Script -->
 @if (old('_modal') === 'edit')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const editModal = new bootstrap.Modal(document.getElementById('editAccountModal'));
    editModal.show();
  });
</script>
@endif
@if ($errors->any())
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var myModal = new bootstrap.Modal(document.getElementById('addAccountModal'));
    myModal.show();
  });
</script>
@endif

@if ($errors->editAccount->any())
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var editModal = new bootstrap.Modal(document.getElementById('editAccountModal'));
    editModal.show();
  });
</script>
@endif

@endsection