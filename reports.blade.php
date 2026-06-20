@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<h2>Reports</h2>
<div class="stats-grid">
    <div class="stat-card"><div class="stat-number">${{ number_format($revenue, 2) }}</div><div class="stat-label">Revenue</div></div>
    <div class="stat-card"><div class="stat-number">{{ $users }}</div><div class="stat-label">Users</div></div>
    <div class="stat-card"><div class="stat-number">{{ $projects }}</div><div class="stat-label">Projects</div></div>
    <div class="stat-card"><div class="stat-number">{{ $completed }}</div><div class="stat-label">Completed</div></div>
    <div class="stat-card"><div class="stat-number">{{ $bids }}</div><div class="stat-label">Bids</div></div>
    <div class="stat-card"><div class="stat-number">{{ $messages }}</div><div class="stat-label">Messages</div></div>
    <div class="stat-card"><div class="stat-number">{{ $alerts }}</div><div class="stat-label">Alerts</div></div>
</div>
@endsection
