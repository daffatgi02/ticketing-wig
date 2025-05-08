@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2>Dashboard</h2>
                <p class="text-muted">Welcome back, {{ Auth::user()->name }}</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">
                                    @if (Auth::user()->isSupport())
                                        Assigned Tickets
                                    @else
                                        Open Tickets
                                    @endif
                                </h6>
                                <h2 class="mb-0">{{ $openTickets }}</h2>
                            </div>
                            <i class="fas fa-ticket-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="{{ route('tickets.index', ['status' => Auth::user()->isSupport() ? 'assigned' : 'open']) }}"
                            class="text-white text-decoration-none"></a>
                        <div class="small text-white"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">In Progress</h6>
                                <h2 class="mb-0">{{ $inProgressTickets }}</h2>
                            </div>
                            <i class="fas fa-spinner fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}"
                            class="text-white text-decoration-none"></a>
                        <div class="small text-white"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Resolved</h6>
                                <h2 class="mb-0">{{ $resolvedTickets }}</h2>
                            </div>
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="{{ route('tickets.index', ['status' => 'resolved']) }}"
                            class="text-white text-decoration-none"></a>
                        <div class="small text-white"></i></div>
                    </div>
                </div>
            </div>
            <!-- Tambahkan card External Support -->
            <div class="col-md-4">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">External Support</h6>
                                <h2 class="mb-0">{{ $externalSupportTickets }}</h2>
                            </div>
                            <i class="fas fa-tools fa-3x opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="{{ route('tickets.index', ['needs_external_support' => '1']) }}"
                            class="text-dark text-decoration-none"></a>
                        <div class="small text-dark"></i></div>
                    </div>
                </div>
            </div>
        </div>

        @if (Auth::user()->isAdmin())
            <div class="row mb-4">
                <!-- Department Statistics -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-building me-1"></i>
                            Top Departments by Tickets
                        </div>
                        <div class="card-body">
                            @if (count($departmentTickets) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Ticket ID</th>
                                                <th>Title</th>
                                                <th>Category</th>
                                                @if (Auth::user()->isAdmin() || Auth::user()->isSupport())
                                                    <th>Created By</th>
                                                @endif
                                                <th>Status</th>
                                                <th>External</th> <!-- Kolom baru -->
                                                <th>Created</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentTickets as $ticket)
                                                <tr>
                                                    <td>{{ $ticket->ticket_id }}</td>
                                                    <td>{{ $ticket->title }}</td>
                                                    <td>{{ $ticket->category->name }}</td>
                                                    @if (Auth::user()->isAdmin() || Auth::user()->isSupport())
                                                        <td>{{ $ticket->user->name }}</td>
                                                    @endif
                                                    <td>
                                                        @if ($ticket->status == 'open')
                                                            <span class="badge bg-info">Open</span>
                                                        @elseif($ticket->status == 'assigned')
                                                            <span class="badge bg-primary">Assigned</span>
                                                        @elseif($ticket->status == 'in_progress')
                                                            <span class="badge bg-warning">In Progress</span>
                                                        @elseif($ticket->status == 'resolved')
                                                            <span class="badge bg-success">Resolved</span>
                                                        @elseif($ticket->status == 'closed')
                                                            <span class="badge bg-secondary">Closed</span>
                                                        @elseif($ticket->status == 'rejected')
                                                            <span class="badge bg-danger">Rejected</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($ticket->needs_external_support)
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="fas fa-tools"></i>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                                    <td>
                                                        <a href="{{ route('tickets.show', $ticket) }}"
                                                            class="btn btn-sm btn-primary">View</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="{{ Auth::user()->isAdmin() || Auth::user()->isSupport() ? '8' : '7' }}"
                                                        class="text-center">No tickets found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">No department data available yet.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Top Ticket Reporters -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-users me-1"></i>
                            Top Ticket Creators
                        </div>
                        <div class="card-body">
                            @if (count($topReporters) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Department</th>
                                                <th>Tickets Created</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($topReporters as $reporter)
                                                <tr>
                                                    <td>{{ $reporter->name }}</td>
                                                    <td>{{ $reporter->department }}</td>
                                                    <td>{{ $reporter->count }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">No ticket creation data available yet.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Recent Tickets
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ticket ID</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        @if (Auth::user()->isAdmin() || Auth::user()->isSupport())
                                            <th>Created By</th>
                                        @endif
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTickets as $ticket)
                                        <tr>
                                            <td>{{ $ticket->ticket_id }}</td>
                                            <td>{{ $ticket->title }}</td>
                                            <td>{{ $ticket->category->name }}</td>
                                            @if (Auth::user()->isAdmin() || Auth::user()->isSupport())
                                                <td>{{ $ticket->user->name }}</td>
                                            @endif
                                            <td>
                                                @if ($ticket->status == 'open')
                                                    <span class="badge bg-info">Open</span>
                                                @elseif($ticket->status == 'assigned')
                                                    <span class="badge bg-primary">Assigned</span>
                                                @elseif($ticket->status == 'in_progress')
                                                    <span class="badge bg-warning">In Progress</span>
                                                @elseif($ticket->status == 'resolved')
                                                    <span class="badge bg-success">Resolved</span>
                                                @elseif($ticket->status == 'closed')
                                                    <span class="badge bg-secondary">Closed</span>
                                                @elseif($ticket->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="{{ route('tickets.show', $ticket) }}"
                                                    class="btn btn-sm btn-primary">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ Auth::user()->isAdmin() || Auth::user()->isSupport() ? '7' : '6' }}"
                                                class="text-center">No tickets found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer small text-muted">
                        @if (!Auth::user()->isSupport())
                            <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary">Create New Ticket</a>
                        @endif
                        <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-secondary ms-2">View All Tickets</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
