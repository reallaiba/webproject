@extends('layouts.app')
@section('title', 'Admin Users')
@section('content')
<h2>Users</h2>
<div class="table-container"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($users as $user)
<tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->role }}</td><td>{{ $user->status }}</td><td>@if(!$user->isAdmin())<form method="post" action="{{ route('admin.users.toggle', $user) }}">@csrf <button class="btn btn-sm btn-danger">Toggle</button></form>@endif</td></tr>
@endforeach
</tbody></table></div>
@endsection
