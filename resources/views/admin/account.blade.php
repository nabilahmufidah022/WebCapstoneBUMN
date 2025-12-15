@extends('layout.index')

@section('content')


<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <button type="button" class="btn btn-primary" id="addAccountBtn">Add Account</button>
  </div>
  <div class="table-container mb-4" >
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Name</th>
            <th>User Type</th>
            <th>Email</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->usertype }}</td>
            <td>{{ $user->email }}</td>
            <td>
              <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}" data-usertype="{{ $user->usertype }}">Edit</button>
              @php $active = isset($user->is_active) ? $user->is_active : true; @endphp
              @if($active)
                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $user->id }}" data-active="1">Non Aktifkan</button>
              @else
                <button type="button" class="btn btn-sm btn-success delete-btn" data-id="{{ $user->id }}" data-active="0">Aktifkan</button>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
  </div>
</div>

  <!-- Add Account Modal -->
  <div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addAccountModalLabel">Add New Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addAccountForm">
            @csrf
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
              <label for="usertype" class="form-label">User Type</label>
              <select class="form-select" id="usertype" name="usertype" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="saveAccountBtn">Save Account</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Account Modal -->
  <div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editAccountModalLabel">Edit Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editAccountForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_user_id" name="user_id">
            <div class="mb-3">
              <label for="edit_name" class="form-label">Name</label>
              <input type="text" class="form-control" id="edit_name" name="name" required>
            </div>
            <div class="mb-3">
              <label for="edit_email" class="form-label">Email</label>
              <input type="email" class="form-control" id="edit_email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="edit_usertype" class="form-label">User Type</label>
              <select class="form-select" id="edit_usertype" name="usertype" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="updateAccountBtn">Update Account</button>
        </div>
      </div>
    </div>
  </div>
@endsection
