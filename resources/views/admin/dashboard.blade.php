@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Admin Dashboard</h2>
            <p class="text-muted">Overview of system statistics and activities</p>
        </div>
    </div>

    <div class="row mb-4">
        <!-- User Statistics Card -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-users me-1"></i>
                    User Statistics
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small">Regular Users</div>
                                            <div class="fs-4">{{ $userCounts['user'] ?? 0 }}</div>
                                        </div>
                                        <i class="fas fa-user-circle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small">IT Support</div>
                                            <div class="fs-4">{{ $userCounts['it_support'] ?? 0 }}</div>
                                        </div>
                                        <i class="fas fa-laptop-code fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small">GA Support</div>
                                            <div class="fs-4">{{ $userCounts['ga_support'] ?? 0 }}</div>
                                        </div>
                                        <i class="fas fa-building fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small">Admins</div>
                                            <div class="fs-4">{{ $userCounts['admin'] ?? 0 }}</div>
                                        </div>
                                        <i class="fas fa-user-shield fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">Manage Users</a>
                </div>
            </div>
        </div>

        <!-- Ticket Statistics Card -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-ticket-alt me-1"></i>
                    Ticket Statistics
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small">Open Tickets</div>
                                            <div class="fs-4">{{ $ticketCounts['open'] ?? 0 }}</div>
                                        </div>
                                        <i class="fas fa-envelope-open fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small">Assigned</div>
                                            <div class="fs-4">{{ $ticketCounts['assigned'] ?? 0 }}</div>
                                        </div>
                                        <i class="fas fa-paper-plane fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small">In Progress</div>
                                            <div class="fs-4">{{ $ticketCounts['in_progress'] ?? 0 }}</div>
                                        </div>
                                        <i class="fas fa-spinner fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small">Resolved</div>
                                            <div class="fs-4">{{ $ticketCounts['resolved'] ?? 0 }}</div>
                                        </div>
                                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-primary">View All Tickets</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Monthly Tickets Chart -->
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Tickets Created in {{ date('Y') }}
                </div>
                <div class="card-body">
                    <canvas id="ticketsChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Tickets -->
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
                                    <th>Created By</th>
                                    <th>Assigned To</th>
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
                                        <td>{{ $ticket->user->name }}</td>
                                        <td>{{ $ticket->assignedTo->name ?? 'Not assigned' }}</td>
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
                                        <td>
                                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-primary">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No tickets found</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare data for monthly tickets chart
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const ticketsData = @json($ticketsPerMonth);

        const ticketsPerMonth = months.map((month, index) => {
            const monthNumber = index + 1;
            return ticketsData[monthNumber] || 0;
        });

        // Create the chart
        const ctx = document.getElementById('ticketsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Tickets Created',
                    data: ticketsPerMonth,
                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
