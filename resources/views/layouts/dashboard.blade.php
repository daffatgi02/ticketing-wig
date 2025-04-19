<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Additional CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .main-content {
            min-height: calc(100vh - 56px);
        }
        .sidebar .nav-link {
            color: #333;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            margin-bottom: 0.25rem;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }
        .sidebar .nav-link.active {
            background-color: #007bff;
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 1.25rem;
            text-align: center;
        }
        .sidebar-heading {
            font-size: 0.85rem;
            text-transform: uppercase;
            padding-left: 1rem;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }
        /* Mobile menu styling */
        .mobile-menu {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .mobile-menu .nav-link {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            display: block;
        }
        .mobile-menu .nav-link:last-child {
            border-bottom: none;
        }
        .mobile-menu .section-title {
            font-weight: 600;
            color: #6c757d;
            margin-top: 15px;
            margin-bottom: 5px;
            padding-left: 15px;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <!-- Single navbar toggler for mobile -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarContent">
                    <!-- Mobile menu - Only visible on small screens -->
                    <div class="d-md-none mobile-menu">
                        <div class="nav flex-column">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="fas fa-home"></i> {{ __('Dashboard') }}
                            </a>

                            <a class="nav-link {{ request()->routeIs('tickets.index') || request()->routeIs('tickets.show') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
                                <i class="fas fa-ticket-alt"></i> {{ __('My Tickets') }}
                            </a>

                            @if(!Auth::user()->isSupport())
                            <a class="nav-link {{ request()->routeIs('tickets.create') ? 'active' : '' }}" href="{{ route('tickets.create') }}">
                                <i class="fas fa-plus-circle"></i> {{ __('Create Ticket') }}
                            </a>
                            @endif

                            @if(Auth::user()->isAdmin())
                                <div class="section-title">{{ __('Administration') }}</div>

                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> {{ __('Admin Dashboard') }}
                                </a>

                                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                    <i class="fas fa-users"></i> {{ __('Manage Users') }}
                                </a>

                                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                                    <i class="fas fa-tags"></i> {{ __('Manage Categories') }}
                                </a>

                                <a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">
                                    <i class="fas fa-building"></i> {{ __('Manage Departments') }}
                                </a>
                            @endif

                            <div class="section-title">{{ __('Account') }}</div>

                            <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user-circle"></i> {{ __('My Profile') }}
                            </a>

                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>

                    <!-- Desktop navbar - Right aligned menu -->
                    <ul class="navbar-nav ms-auto d-none d-md-flex">
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-circle me-2"></i>{{ __('Profile') }}
                                </a>

                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form-desktop').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>{{ __('Logout') }}
                                </a>

                                <form id="logout-form-desktop" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <!-- Desktop sidebar (hidden on mobile) -->
                <div class="col-md-3 col-lg-2 d-none d-md-block bg-light sidebar py-3">
                    @include('layouts.sidebar')
                </div>

                <!-- Main content -->
                <main class="col-12 col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4 main-content">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
