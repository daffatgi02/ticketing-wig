<nav class="d-md-block sidebar collapse">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                    <i class="fas fa-home"></i>
                    {{ __('Dashboard') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tickets.index') || request()->routeIs('tickets.show') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
                    <i class="fas fa-ticket-alt"></i>
                    {{ __('My Tickets') }}
                </a>
            </li>

            @if(!Auth::user()->isSupport())
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tickets.create') ? 'active' : '' }}" href="{{ route('tickets.create') }}">
                    <i class="fas fa-plus-circle"></i>
                    {{ __('Create Ticket') }}
                </a>
            </li>
            @endif

            @if(Auth::user()->isAdmin())
                <li class="nav-item mt-3">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-2 mb-1 text-muted">
                        <span>{{ __('Administration') }}</span>
                    </h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        {{ __('Admin Dashboard') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i>
                        {{ __('Manage Users') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-tags"></i>
                        {{ __('Manage Categories') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">
                        <i class="fas fa-building"></i>
                        {{ __('Manage Departments') }}
                    </a>
                </li>
            @endif

            <li class="nav-item mt-3">
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-2 mb-1 text-muted">
                    <span>{{ __('Account') }}</span>
                </h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                    <i class="fas fa-user-circle"></i>
                    {{ __('My Profile') }}
                </a>
            </li>
        </ul>
    </div>
</nav>
