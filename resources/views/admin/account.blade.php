@extends('layout.index')

@section('content')
<div class="table-container">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Name</th>
          <th>User Type</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $user->name }}</td>
          <td>{{ $user->usertype }}</td>
          <td>{{ $user->email }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection
