@extends("layout.main")
@section("title", "Account")
@section("content")
@php
$prefix = Auth::user()->role === 'admin' ? 'admin.' : '';
@endphp
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

<!-- Top Navbar -->
<div class="top-navbar">
  <img src="{{ asset('images/logoo.png') }}" alt="" class="logo-nav">
  <div class="user-greeting">
    Hello, {{ Auth::user()->first_name }}
  </div>
</div>

<!-- Sidebar + Content -->
<div class="main-wrapper">
  @include('layout.sidebar')

  <div class="main-content">
    <div class="container mt-4">
      <h2>Manage Accounts</h2>

      <!-- Add Account Button -->
      <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAccountModal">
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
              <button
                class="btn btn-primary btn-sm editBtn"
                data-id="{{ $user->id }}"
                data-first-name="{{ $user->first_name }}"
                data-last-name="{{ $user->last_name }}"
                data-role="{{ $user->role }}"
                data-email="{{ $user->email }}"
                data-bs-toggle="modal" data-bs-target="#editAccountModal">
                Edit
              </button>
              <form action="{{ route($prefix . 'accounts.destroy', $user->id) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Are you sure you want to delete this account?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

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
    <div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form method="POST" id="editAccountForm" action="{{ session('edit_user_id') ? url('admin/accounts/' . session('edit_user_id')) : '' }}">
          @csrf
          @method('PUT')
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editAccountModalLabel">Edit Account</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <!-- Edit Account Error Block -->
              @if ($errors->editAccount->any())
              <div class="alert alert-danger">
                <ul>
                  @foreach ($errors->editAccount->all() as $error)
                  <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
              @endif

              <input type="hidden" id="editUserId" name="id">
              <input type="text" name="first_name" id="editFirstName" class="form-control mb-2" value="{{ old('first_name') }}" placeholder="First Name" required>
              <input type="text" name="last_name" id="editLastName" class="form-control mb-2" value="{{ old('last_name') }}" placeholder="Last Name" required>
              <input type="email" name="email" id="editEmail" class="form-control mb-2" value="{{ old('email') }}" placeholder="Email" required>
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

    <!-- Edit Button Script -->
    <script>
      document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', function() {
          const userId = this.getAttribute('data-id');
          const firstName = this.getAttribute('data-first-name');
          const lastName = this.getAttribute('data-last-name');
          const userEmail = this.getAttribute('data-email');

          document.getElementById('editUserId').value = userId;
          document.getElementById('editFirstName').value = firstName;
          document.getElementById('editLastName').value = lastName;
          document.getElementById('editEmail').value = userEmail;

          document.getElementById('editAccountForm').action = `{{ url(Auth::user()->role == 'admin' ? 'admin/accounts' : 'accounts') }}/${userId}`;
        });
      });
    </script>

    <!-- Auto Open Correct Modal Script -->
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