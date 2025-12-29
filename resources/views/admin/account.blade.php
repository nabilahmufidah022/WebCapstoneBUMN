@extends('layout.index')

@section('content')

<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">Account Management</h4>
    <button type="button" class="btn btn-primary" id="addAccountBtn">
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
        <tbody>
          @foreach($users as $user)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td class="fw-semibold">{{ $user->name }}</td>
            <td>{{ ucfirst($user->usertype) }}</td>
            <td>{{ $user->email }}</td>
            <td class="text-center">
              <button class="btn btn-warning btn-sm edit-btn">Edit</button>
              <button class="btn btn-danger btn-sm">Non Aktifkan</button>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>

@endsection
