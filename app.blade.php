<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'FreelanceHub')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="{{ request()->routeIs('admin.*') ? 'admin-page' : '' }}">
@php
    $hasAdminSession = session()->has('admin_user_id');
@endphp
@auth
    @php
        $unreadMessages = \App\Models\Message::where('receiver_id', auth()->id())->where('is_read', false)->count();
        $unreadNotifications = \App\Models\MarketplaceNotification::where('user_id', auth()->id())->where('is_read', false)->count();
    @endphp
@endauth
<nav class="navbar">
    <a class="navbar-brand" href="{{ request()->routeIs('admin.*') || $hasAdminSession || (auth()->check() && auth()->user()->isAdmin()) ? route('admin.dashboard') : route('home') }}">
        {{ request()->routeIs('admin.*') ? 'Admin Panel' : 'FreelanceHub' }}
    </a>
    <div class="navbar-links">
        @auth
            @if(auth()->user()->isAdmin())
                <a class="admin-pill-link" href="{{ route('admin.dashboard') }}">Admin Panel</a>
                <a href="{{ route('admin.users') }}">Users</a>
                <a href="{{ route('admin.projects') }}">Projects</a>
                <a href="{{ route('admin.messages') }}">Messages</a>
                <a href="{{ route('admin.alerts') }}">Alerts</a>
                <a href="{{ route('admin.reports') }}">Reports</a>
            @else
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('projects.index') }}">Projects</a>
                <a href="{{ route('gigs.index') }}">Gigs</a>
                <a class="nav-icon-link" href="{{ route('messages') }}">Messages @if($unreadMessages)<span class="nav-badge">{{ $unreadMessages }}</span>@endif</a>
                <a class="nav-icon-link" href="{{ route('notifications') }}">Alerts @if($unreadNotifications)<span class="nav-badge">{{ $unreadNotifications }}</span>@endif</a>
                <a class="admin-pill-link" href="{{ $hasAdminSession ? route('admin.dashboard') : route('login', ['admin' => 1]) }}">Admin Panel</a>
                @if(auth()->user()->isClient())
                    <a href="{{ route('cart') }}">Cart</a>
                @endif
            @endif
            <span class="nav-user">Hi, {{ auth()->user()->name }}</span>
            <form method="post" action="{{ route('logout') }}">@csrf <button class="btn btn-sm btn-outline">Logout</button></form>
        @else
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('gigs.index') }}">Browse Gigs</a>
            <a href="{{ route('projects.index') }}">Projects</a>
            <a class="admin-pill-link" href="{{ route('login', ['admin' => 1]) }}">Admin Panel</a>
            <a href="{{ route('login') }}">Login</a>
            <a class="btn btn-sm btn-primary" href="{{ route('register') }}">Register</a>
        @endauth
    </div>
</nav>

@if(request()->routeIs('admin.*'))
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-sidebar-title">
                <strong>Admin Panel</strong>
                <span>Manage marketplace</span>
            </div>
            <a class="sidebar-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="sidebar-link" href="{{ route('admin.users') }}">Manage Users</a>
            <a class="sidebar-link" href="{{ route('admin.projects') }}">Manage Projects</a>
            <a class="sidebar-link" href="{{ route('admin.gigs') }}">Manage Gigs</a>
            <a class="sidebar-link" href="{{ route('admin.orders') }}">Orders</a>
            <a class="sidebar-link" href="{{ route('admin.messages') }}">Messages</a>
            <a class="sidebar-link" href="{{ route('admin.alerts') }}">Alerts</a>
            <a class="sidebar-link" href="{{ route('admin.reports') }}">Reports</a>
            <a class="sidebar-link" href="{{ route('home') }}">View Website</a>
        </aside>
        <main class="admin-content">
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="alert alert-error">{{ session('error') }}</div> @endif
            @if($errors->any()) <div class="alert alert-error">{{ $errors->first() }}</div> @endif
            @yield('content')
        </main>
    </div>
@else
    <main class="main-content">
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-error">{{ session('error') }}</div> @endif
        @if($errors->any()) <div class="alert alert-error">{{ $errors->first() }}</div> @endif
        @yield('content')
    </main>
@endif
</body>
</html>
