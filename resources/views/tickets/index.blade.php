@extends('layouts.dashboard')

@section('title', 'My Tickets')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Tickets</h2>
        </div>
        <div class="col-md-6 text-end">
            @if(!Auth::user()->isSupport())
            <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Create New Ticket
            </a>
            @endif
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == '' || request('status') == 'all' ? 'active' : '' }}" href="{{ route('tickets.index', ['status' => 'all']) }}">All</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == 'open' ? 'active' : '' }}" href="{{ route('tickets.index', ['status' => 'open']) }}">Open</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == 'assigned' ? 'active' : '' }}" href="{{ route('tickets.index', ['status' => 'assigned']) }}">Assigned</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == 'in_progress' ? 'active' : '' }}" href="{{ route('tickets.index', ['status' => 'in_progress']) }}">In Progress</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == 'resolved' ? 'active' : '' }}" href="{{ route('tickets.index', ['status' => 'resolved']) }}">Resolved</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == 'closed' ? 'active' : '' }}" href="{{ route('tickets.index', ['status' => 'closed']) }}">Closed</a>
                        </li>
                    </ul>
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
                                    <th>Last Updated</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
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
                                        <td>{{ $ticket->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ $ticket->updated_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-primary">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ Auth::user()->isAdmin() || Auth::user()->isSupport() ? '8' : '7' }}" class="text-center">No tickets found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
