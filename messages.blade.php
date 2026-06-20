@extends('layouts.app')
@section('title', 'Admin Messages')
@section('content')
<div class="page-header">
    <div>
        <h1>Messages</h1>
        <p class="text-muted">All client and freelancer conversations in the marketplace.</p>
    </div>
    <a class="btn btn-outline-dark" href="{{ route('admin.dashboard') }}">Back to Admin</a>
</div>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>From</th>
                <th>To</th>
                <th>Project</th>
                <th>Message</th>
                <th>Status</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse($messages as $message)
                <tr>
                    <td>{{ $message->sender->name }}</td>
                    <td>{{ $message->receiver->name }}</td>
                    <td>{{ $message->project?->title ?? '-' }}</td>
                    <td>{{ Str::limit($message->message, 90) }}</td>
                    <td><span class="badge badge-{{ $message->is_read ? 'active' : 'pending' }}">{{ $message->is_read ? 'Read' : 'Unread' }}</span></td>
                    <td>{{ $message->created_at->diffForHumans() }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No messages yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $messages->links() }}
@endsection
