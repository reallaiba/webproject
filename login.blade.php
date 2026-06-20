@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="form-container">
    <h2>{{ request('admin') ? 'Admin Login' : 'Login' }}</h2>
    @if(request('admin'))
        <div class="login-demo-card">
            <strong>Admin Panel Access</strong>
            <span>Email: admin@freelancehub.com</span>
            <span>Password: admin123</span>
        </div>
    @endif
    <form method="post" action="{{ route('login.store') }}">
        @csrf
        @if(request('admin'))
            <input type="hidden" name="admin_login" value="1">
        @endif
        <div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email', request('admin') ? 'admin@freelancehub.com' : '') }}" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" value="{{ request('admin') ? 'admin123' : '' }}" required></div>
        <div class="form-check"><input type="checkbox" name="remember" id="remember"> <label for="remember">Remember me</label></div>
        <button class="btn btn-primary btn-block">Login</button>
    </form>
    <div class="form-footer">No account? <a href="{{ route('register') }}">Register here</a></div>
</div>
@endsection
