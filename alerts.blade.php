@extends('layouts.app')
@section('title', 'Admin Alerts')
@section('content')
<div class="page-header">
    <div>
        <h1>Alerts</h1>
        <p class="text-muted">All marketplace notifications created by bids, messages, hires, and project updates.</p>
    </div>
    <a class="btn btn-outline-dark" href="{{ route('admin.dashboard') }}">Back to Admin</a>
</div>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Type</th>
                <th>Title</th>
                <th>Message</th>
                <th>Status</th>
                <th>Time</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($alerts as $alert)
                <tr>
                    <td>{{ $alert->user?->name ?? 'User deleted' }}</td>
                    <td><span class="badge badge-active">{{ $alert->type }}</span></td>
                    <td>{{ $alert->title }}</td>
                    <td>{{ Str::limit($alert->message, 90) }}</td>
                    <td><span class="badge badge-{{ $alert->is_read ? 'active' : 'pending' }}">{{ $alert->is_read ? 'Read' : 'Unread' }}</span></td>
                    <td>{{ $alert->created_at->diffForHumans() }}</td>
                    <td>@if($alert->link)<a class="btn btn-sm btn-primary" href="{{ $alert->link }}">Open</a>@endif</td>
                </tr>
            @empty
                <tr><td colspan="7">No alerts yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
