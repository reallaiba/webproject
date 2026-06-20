@extends('layouts.app')
@section('title', 'FreelanceHub')
@section('content')
<section class="hero">
    <h1>FreelanceHub</h1>
    <p>Post projects, hire freelancers, sell gigs, and manage work in one marketplace.</p>
    <div class="hero-actions">
        <a class="btn btn-primary" href="{{ route('projects.index') }}">Browse Projects</a>
        <a class="btn btn-light" href="{{ route('gigs.index') }}">Browse Gigs</a>
    </div>
</section>
<div class="stats-grid">
    @foreach($stats as $stat)
        <a class="stat-card stat-link-card" href="{{ $stat['link'] }}">
            <div class="stat-number">{{ $stat['value'] }}</div>
            <div class="stat-label">{{ $stat['label'] }}</div>
            <p>{{ $stat['help'] }}</p>
        </a>
    @endforeach
</div>
<section class="section">
    <h2>Categories</h2>
    <div class="cards">
        @foreach($categories as $category)
            <a class="card category-link-card" href="{{ route('projects.index', ['category' => $category->id]) }}">
                <h3>{{ $category->icon }} {{ $category->name }}</h3>
                <p>Open projects in this category</p>
            </a>
        @endforeach
    </div>
</section>
<section class="section">
    <h2>Active Freelancers</h2>
    <div class="cards">
        @foreach($freelancers as $freelancer)
            <div class="card">
                <h3>{{ $freelancer->name }}</h3>
                <p>{{ $freelancer->skills ?: 'Freelancer' }}</p>
                <a class="btn btn-sm btn-outline-dark" href="{{ route('messages', ['with' => $freelancer->id]) }}">Message</a>
            </div>
        @endforeach
    </div>
</section>
@endsection
