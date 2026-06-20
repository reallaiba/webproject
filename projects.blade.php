@extends('layouts.app')
@section('title', 'Admin Projects')
@section('content')
<h2>Projects</h2>
<div class="table-container"><table><thead><tr><th>Title</th><th>Client</th><th>Status</th><th>Budget</th><th>Bids</th><th>Hired</th><th></th></tr></thead><tbody>
@foreach($projects as $project)<tr><td>{{ $project->title }}</td><td>{{ $project->client->name }}</td><td><span class="badge badge-{{ $project->status }}">{{ str_replace('_', ' ', $project->status) }}</span></td><td>${{ number_format($project->budget, 2) }}</td><td>{{ $project->bids_count }}</td><td>{{ $project->hiredFreelancer?->name ?? '-' }}</td><td><a class="btn btn-sm btn-primary" href="{{ route('projects.show', $project) }}">View</a></td></tr>@endforeach
</tbody></table></div>
@endsection
