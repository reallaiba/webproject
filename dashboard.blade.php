@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div class="admin-hero">
    <div>
        <span>FreelanceHub Control Room</span>
        <h1>Admin Panel</h1>
        <p>Manage users, projects, bids, messages, alerts, orders, and marketplace reports from one place.</p>
    </div>
</div>
<div class="dashboard-actions">
    <a class="btn btn-primary" href="{{ route('admin.users') }}">Users</a>
    <a class="btn btn-primary" href="{{ route('admin.projects') }}">Projects</a>
    <a class="btn btn-primary" href="{{ route('admin.gigs') }}">Gigs</a>
    <a class="btn btn-primary" href="{{ route('admin.orders') }}">Orders</a>
    <a class="btn btn-primary" href="{{ route('admin.messages') }}">Messages</a>
    <a class="btn btn-primary" href="{{ route('admin.alerts') }}">Alerts</a>
    <a class="btn btn-primary" href="{{ route('admin.reports') }}">Reports</a>
</div>
<div class="stats-grid">@foreach($stats as $label => $value)<div class="stat-card"><div class="stat-number">{{ $value }}</div><div class="stat-label">{{ ucfirst($label) }}</div></div>@endforeach</div>
<div class="admin-overview-grid">
    <div class="card">
        <h3>Recent Projects</h3>
        <div class="compact-list">
            @foreach($recentProjects as $project)
                <a href="{{ route('projects.show', $project) }}">
                    <strong>{{ $project->title }}</strong>
                    <span>{{ $project->client->name }} - {{ $project->bids_count }} bids - {{ $project->status }}</span>
                </a>
            @endforeach
        </div>
    </div>
    <div class="card">
        <h3>Recent Bids</h3>
        <div class="compact-list">
            @foreach($recentBids as $bid)
                <a href="{{ route('projects.show', $bid->project) }}">
                    <strong>${{ number_format($bid->bid_amount, 2) }} by {{ $bid->freelancer->name }}</strong>
                    <span>{{ $bid->project->title }} - {{ $bid->status }}</span>
                </a>
            @endforeach
        </div>
    </div>
    <div class="card">
        <h3>Recent Users</h3>
        <div class="compact-list">
            @foreach($recentUsers as $user)
                <a href="{{ route('admin.users') }}">
                    <strong>{{ $user->name }}</strong>
                    <span>{{ $user->email }} - {{ $user->role }} - {{ $user->status }}</span>
                </a>
            @endforeach
        </div>
    </div>
    <div class="card">
        <h3>Recent Messages</h3>
        <div class="compact-list">
            @foreach($recentMessages as $message)
                <a href="{{ route('admin.messages') }}">
                    <strong>{{ $message->sender->name }} to {{ $message->receiver->name }}</strong>
                    <span>{{ Str::limit($message->message, 80) }}</span>
                </a>
            @endforeach
        </div>
    </div>
    <div class="card">
        <h3>Recent Alerts</h3>
        <div class="compact-list">
            @foreach($recentAlerts as $alert)
                <a href="{{ route('admin.alerts') }}">
                    <strong>{{ $alert->title }}</strong>
                    <span>{{ $alert->user?->name ?? 'User' }} - {{ Str::limit($alert->message, 75) }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
