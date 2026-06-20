@extends('layouts.app')
@section('title', 'Complete Project')
@section('content')
<div class="form-container">
    <h2>Complete Project</h2>
    <p>{{ $project->title }} for {{ $project->hiredFreelancer->name }}</p>
    <form method="post" action="{{ route('projects.complete.store', $project) }}">
        @csrf
        <div class="form-group"><label>Rating</label><select name="rating" required><option value="5">5</option><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option></select></div>
        <div class="form-group"><label>Review</label><textarea name="comment"></textarea></div>
        <button class="btn btn-success btn-block">Complete Project</button>
    </form>
</div>
@endsection
