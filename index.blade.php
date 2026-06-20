@extends('layouts.app')
@section('title', 'Projects')
@section('content')
<div class="page-head">
    <h2>Projects</h2>
    @auth @if(auth()->user()->isClient())<a class="btn btn-primary" href="{{ route('projects.create') }}">Post Project</a>@endif @endauth
</div>
<form class="search-bar" method="get" action="{{ route('projects.index') }}">
    <select name="category" onchange="this.form.submit()">
        <option value="">All categories</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    <select name="status" onchange="this.form.submit()">
        <option value="">All project status</option>
        <option value="open" @selected(request('status') === 'open')>Open</option>
        <option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option>
        <option value="completed" @selected(request('status') === 'completed')>Completed</option>
    </select>
    @if(request('category') || request('status'))
        <a class="btn btn-sm btn-outline-dark" href="{{ route('projects.index') }}">Clear</a>
    @endif
</form>
<div class="cards">
    @forelse($projects as $project)
        <div class="card">
            <h3>{{ $project->title }}</h3>
            <p class="text-muted">{{ $project->category?->name ?? 'General' }} - {{ $project->client->name }}</p>
            <p>{{ Str::limit($project->description, 120) }}</p>
            <p class="card-price">${{ number_format($project->budget, 2) }}</p>
            <p><span class="badge badge-{{ $project->status }}">{{ str_replace('_', ' ', $project->status) }}</span> <span class="text-muted">{{ $project->bids_count }} bid{{ $project->bids_count === 1 ? '' : 's' }}</span></p>
            <a class="btn btn-sm btn-primary" href="{{ route('projects.show', $project) }}">View</a>
            @auth
                @if(auth()->user()->isFreelancer() && $project->status === 'open')
                    <a class="btn btn-sm btn-outline-dark" href="{{ route('bids.create', $project) }}">Bid</a>
                @endif
            @endauth
        </div>
    @empty
        <div class="card empty-state-card">
            <h3>No projects found</h3>
            <p>Try another category or clear the filters to see available marketplace projects.</p>
            <a class="btn btn-sm btn-outline-dark" href="{{ route('projects.index') }}">Clear Filters</a>
        </div>
    @endforelse
</div>
{{ $projects->links() }}
@endsection
