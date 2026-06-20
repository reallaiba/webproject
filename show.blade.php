@extends('layouts.app')
@section('title', $project->title)
@section('content')
<div class="project-detail">
    <div class="project-detail-header">
        <div><h1>{{ $project->title }}</h1><p class="text-muted">Posted by {{ $project->client->name }} - {{ $project->created_at->diffForHumans() }}</p></div>
        <div class="project-detail-budget"><span class="budget-label">Budget</span><span class="budget-amount">${{ number_format($project->budget, 2) }}</span></div>
    </div>
    <div class="project-detail-grid">
        <div class="project-detail-main card">
            <h3>Description</h3>
            <p>{{ $project->description }}</p>
            @if($project->attachment)<p><a href="{{ Storage::url($project->attachment) }}">Download attachment</a></p>@endif
        </div>
        <div class="project-detail-sidebar card">
            <p>Status: <span class="badge">{{ str_replace('_', ' ', $project->status) }}</span></p>
            <p>Bids: {{ $project->bids->count() }}</p>
            @if($project->hiredFreelancer)<p>Hired: {{ $project->hiredFreelancer->name }}</p>@endif
            @auth
                @if(auth()->id() === $project->client_id)
                    <a class="btn btn-primary btn-block" href="{{ route('projects.bids', $project) }}">View Bids</a>
                    @if($project->status === 'in_progress')<a class="btn btn-success btn-block" href="{{ route('projects.complete', $project) }}">Complete & Review</a>@endif
                @elseif(auth()->user()->isFreelancer() && $project->status === 'open' && !$project->bids->where('freelancer_id', auth()->id())->count())
                    <a class="btn btn-primary btn-block" href="{{ route('bids.create', $project) }}">Submit Bid</a>
                @endif
                @if($project->hired_freelancer_id && in_array(auth()->id(), [$project->client_id, $project->hired_freelancer_id]))
                    <a class="btn btn-outline-dark btn-block" href="{{ route('messages', ['with' => auth()->id() === $project->client_id ? $project->hired_freelancer_id : $project->client_id]) }}">Chat</a>
                @endif
            @endauth
        </div>
    </div>
    <div class="card bid-list-card">
        <div class="page-header">
            <div>
                <h3>Freelancer Bids</h3>
                <p class="text-muted">{{ $project->bids->count() }} proposal{{ $project->bids->count() === 1 ? '' : 's' }} received for this project.</p>
            </div>
            @auth
                @if(auth()->user()->isFreelancer() && $project->status === 'open' && !$project->bids->where('freelancer_id', auth()->id())->count())
                    <a class="btn btn-sm btn-primary" href="{{ route('bids.create', $project) }}">Submit Bid</a>
                @endif
            @endauth
        </div>
        @forelse($project->bids as $bid)
            <div class="bid-row">
                <div>
                    <h4>{{ $bid->freelancer->name }}</h4>
                    <p class="text-muted">{{ $bid->freelancer->skills ?: 'Freelancer' }} - {{ $bid->created_at->diffForHumans() }}</p>
                    <p>{{ $bid->proposal }}</p>
                </div>
                <div class="bid-row-side">
                    <strong>${{ number_format($bid->bid_amount, 2) }}</strong>
                    <span class="badge badge-{{ $bid->status }}">{{ $bid->status }}</span>
                    @auth
                        @if(auth()->id() === $project->client_id && $project->status === 'open' && $bid->status === 'pending')
                            <form method="post" action="{{ route('bids.hire', $bid) }}">
                                @csrf
                                <button class="btn btn-sm btn-success">Hire</button>
                            </form>
                        @endif
                        @if(in_array(auth()->id(), [$project->client_id, $bid->freelancer_id]))
                            <a class="btn btn-sm btn-outline-dark" href="{{ route('messages', ['with' => auth()->id() === $bid->freelancer_id ? $project->client_id : $bid->freelancer_id]) }}">Message</a>
                        @endif
                    @endauth
                </div>
            </div>
        @empty
            <p class="text-muted">No bids yet. Freelancers can submit a bid while this project is open.</p>
        @endforelse
    </div>
</div>
@endsection
