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
                            <h6 class="card-title">Open Tickets</h6>
                            <h2 class="mb-0">{{ $openTickets }}</h2>
                        </div>
                        <i class="fas fa-ticket-alt fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="{{ route('tickets.index', ['status' => 'open']) }}" class="text-white text-decoration-none">View details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
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
                    <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}" class="text-white text-decoration-none">View details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
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
                    <a href="{{ route('tickets.index', ['status' => 'resolved']) }}" class="text-white text-decoration-none">View details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

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
                                    @if(Auth::user()->isAdmin() || Auth::user()->isSupport())
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
                                        @if(Auth::user()->isAdmin() || Auth::user()->isSupport())
                                            <td>{{ $ticket->user->name }}</td>
                                        @endif
                                        <td>
                                            @if($ticket->status == 'open')
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
                                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-primary">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ Auth::user()->isAdmin() || Auth::user()->isSupport() ? '7' : '6' }}" class="text-center">No tickets found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer small text-muted">
                    <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary">Create New Ticket</a>
                    <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-secondary ms-2">View All Tickets</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
