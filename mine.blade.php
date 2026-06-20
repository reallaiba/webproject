@extends('layouts.app')
@section('title', 'My Projects')
@section('content')
<h2>My Projects</h2>
<div class="table-container"><table><thead><tr><th>Title</th><th>Budget</th><th>Status</th><th>Bids</th><th></th></tr></thead><tbody>
@foreach($projects as $project)
<tr><td>{{ $project->title }}</td><td>${{ number_format($project->budget, 2) }}</td><td>{{ $project->status }}</td><td>{{ $project->bids_count }}</td><td><a href="{{ route('projects.show', $project) }}">Open</a></td></tr>
@endforeach
</tbody></table></div>
@endsection
