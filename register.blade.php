@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div class="form-container">
    <h2>Create Account</h2>
    <form method="post" action="{{ route('register.store') }}">
        @csrf
        <div class="form-group"><label>Name</label><input name="name" value="{{ old('name') }}" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
        <div class="form-group"><label>Confirm Password</label><input type="password" name="password_confirmation" required></div>
        <div class="form-group"><label>I want to</label><select name="role"><option value="client">Hire Freelancers</option><option value="freelancer">Offer Services</option></select></div>
        <button class="btn btn-primary btn-block">Register</button>
    </form>
</div>
@endsection
