@extends('layout.index')

@section('content')

<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">Account Management</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal">
      Add Account
    </button>
  </div>

  <div class="card border-0 shadow-sm" style="border-radius:12px">
    <div class="table-responsive p-3">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Name</th>
            <th>User Type</th>
            <th>Email</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody id="accountTableBody">
          @foreach($users as $user)
          <tr id="row-{{ $user->id }}">
            <td>{{ $loop->iteration }}</td>
            <td class="fw-semibold">{{ $user->name }}</td>
            <td class="text-capitalize">{{ $user->usertype }}</td>
            <td>{{ $user->email }}</td>
            <td class="text-center">
              <button class="btn btn-warning btn-sm edit-btn" 
                      data-id="{{ $user->id }}"
                      data-url="{{ route('account.edit', $user->id) }}">
                  Edit
              </button>
              <button class="btn {{ $user->is_active ? 'btn-danger' : 'btn-success' }} btn-sm toggle-status-btn" 
                      data-id="{{ $user->id }}"
                      data-url="{{ route('account.delete', $user->id) }}">
                  {{ $user->is_active ? 'Non Aktifkan' : 'Aktifkan' }}
              </button>
              <button class="btn btn-outline-danger btn-sm delete-btn" 
                      data-id="{{ $user->id }}"
                      data-url="{{ route('account.destroy', $user->id) }}">
                  Hapus
              </button>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- Create Account Modal -->
<div class="modal fade" id="createAccountModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Add New Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createAccountForm">
        <div class="modal-body">
          <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required minlength="8">
          </div>
          <div class="mb-3">
              <label class="form-label">User Type</label>
              <select name="usertype" class="form-select" required>
                  <option value="user">User</option>
                  <option value="admin">Admin</option>
              </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Account</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Account Modal -->
<div class="modal fade" id="editAccountModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Edit Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editAccountForm">
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-body">
          <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" id="edit_name" class="form-control" required>
          </div>
          <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" id="edit_email" class="form-control" required>
          </div>
          <div class="mb-3">
              <label class="form-label">User Type</label>
              <select name="usertype" id="edit_usertype" class="form-select" required>
                  <option value="user">User</option>
                  <option value="admin">Admin</option>
              </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update Account</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Create Account
    $('#createAccountForm').submit(function(e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            url: "{{ route('account.store') }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                alert(response.message);
                location.reload();
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = '';
                for (let field in errors) {
                    errorMessage += errors[field][0] + '\n';
                }
                alert(errorMessage);
            }
        });
    });

    // Edit Button Click
    $('.edit-btn').click(function() {
        let url = $(this).data('url');
        $.get(url, function(data) {
            $('#edit_id').val(data.id);
            $('#edit_name').val(data.name);
            $('#edit_email').val(data.email);
            $('#edit_usertype').val(data.usertype);
            $('#editAccountModal').modal('show');
        });
    });

    // Update Account
    $('#editAccountForm').submit(function(e) {
        e.preventDefault();
        let id = $('#edit_id').val();
        let formData = $(this).serialize();
        let url = "{{ url('account') }}/" + id; // Construct the update URL manually or pass it in data

        $.ajax({
            url: url,
            type: 'PUT',
            data: formData,
            success: function(response) {
                alert(response.message);
                location.reload();
            },
            error: function(xhr) {
                alert('Error updating account');
            }
        });
    });

    // Toggle Status (Deactivate/Activate)
    $('.toggle-status-btn').click(function() {
        if(!confirm('Are you sure you want to change the status of this account?')) return;
        
        let url = $(this).data('url');
        
        $.ajax({
            url: url,
            type: 'DELETE',
            success: function(response) {
                alert(response.message);
                location.reload();
            },
            error: function(xhr) {
                alert('Error changing status');
            }
        });
    });

    // Delete Permanently
    $('.delete-btn').click(function() {
        if(!confirm('WARNING: Are you sure you want to PERMANENTLY delete this account? This action cannot be undone and will delete related Mitra data.')) return;
        
        let url = $(this).data('url');
        
        $.ajax({
            url: url,
            type: 'DELETE',
            success: function(response) {
                alert(response.message);
                location.reload();
            },
            error: function(xhr) {
                alert('Error deleting account');
            }
        });
    });
});
</script>

@endsection
