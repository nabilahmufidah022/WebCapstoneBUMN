@extends('layout.index')

@section('content')
<div class="table-container">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Mitra</th>
          <th>Email</th>
          <th>Nomor Hp</th>
          <th>Kategori</th>
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
