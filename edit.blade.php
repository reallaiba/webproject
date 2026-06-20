@extends('layouts.app')
@section('title', 'Profile')
@section('content')
<div class="form-container">
    <h2>Edit Profile</h2>
    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group"><label>Name</label><input name="name" value="{{ $user->name }}" required></div>
        <div class="form-group"><label>Bio</label><textarea name="bio">{{ $user->bio }}</textarea></div>
        <div class="form-group"><label>Skills</label><input name="skills" value="{{ $user->skills }}"></div>
        <div class="form-group"><label>Location</label><input name="location" value="{{ $user->location }}"></div>
        <div class="form-group"><label>Avatar</label><input type="file" name="avatar"></div>
        <button class="btn btn-primary btn-block">Save Profile</button>
    </form>
</div>
@endsection
