@extends('layouts.dashboard')

@section('title', 'Ticket Details')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>Ticket: {{ $ticket->ticket_id }}</h2>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Tickets
                </a>
                @if (Auth::user()->isAdmin())
                    <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to delete this ticket?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Ticket Details -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-ticket-alt me-1"></i>
                            {{ $ticket->title }}
                        </div>
                        <div>
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
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5>Description:</h5>
                            <p class="card-text">{{ $ticket->description }}</p>
                        </div>

                        @if ($ticket->rejection_reason)
                            <div class="mb-4">
                                <h5>Rejection Reason:</h5>
                                <p class="card-text text-danger">{{ $ticket->rejection_reason }}</p>
                            </div>
                        @endif

                        @if (count($ticket->attachments) > 0)
                            <div class="mb-4">
                                <h5>Attachments:</h5>
                                <div class="d-flex flex-wrap">
                                    @foreach ($ticket->attachments->where('comment_id', null) as $attachment)
                                        <div class="me-3 mb-3">
                                            <div class="card" style="width: 150px;">
                                                @if (in_array($attachment->filetype, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']))
                                                    <img src="{{ Storage::url($attachment->filepath) }}"
                                                        class="card-img-top" alt="{{ $attachment->filename }}"
                                                        style="height: 100px; object-fit: cover;">
                                                @else
                                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                                        style="height: 100px;">
                                                        <i class="fas fa-file fa-3x text-secondary"></i>
                                                    </div>
                                                @endif
                                                <div class="card-body p-2">
                                                    <p class="card-text" style="font-size: 0.8rem;">
                                                        {{ Str::limit($attachment->filename, 15) }}</p>
                                                    <a href="{{ Storage::url($attachment->filepath) }}"
                                                        class="btn btn-sm btn-primary" target="_blank">View</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer text-muted">
                        <div class="row">
                            <div class="col-md-6">
                                <small>Created by: {{ $ticket->user->name }}</small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small>Created: {{ $ticket->created_at->format('M d, Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-comments me-1"></i>
                        Conversation
                    </div>
                    <div class="card-body">
                        @forelse($ticket->comments as $comment)
                            <div
                                class="mb-4 p-3 {{ $comment->user_id == Auth::id() ? 'bg-light rounded' : 'border-start border-4 border-primary ps-3' }}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong>{{ $comment->user->name }}</strong>
                                        @if ($comment->user->role == 'admin')
                                            <span class="badge bg-danger ms-1">HC - Admin</span>
                                        @elseif($comment->user->role == 'it_support')
                                            <span class="badge bg-primary ms-1">IT Support</span>
                                        @elseif($comment->user->role == 'ga_support')
                                            <span class="badge bg-success ms-1">GA Support</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $comment->created_at->format('M d, Y H:i') }}</small>
                                </div>

                                <p class="mb-3">{{ $comment->comment }}</p>

                                @if (count($comment->attachments) > 0)
                                    <div class="d-flex flex-wrap">
                                        @foreach ($comment->attachments as $attachment)
                                            <div class="me-3 mb-3">
                                                <div class="card" style="width: 150px;">
                                                    @if (in_array($attachment->filetype, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']))
                                                        <img src="{{ Storage::url($attachment->filepath) }}"
                                                            class="card-img-top" alt="{{ $attachment->filename }}"
                                                            style="height: 80px; object-fit: cover;">
                                                    @else
                                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                                            style="height: 80px;">
                                                            <i class="fas fa-file fa-2x text-secondary"></i>
                                                        </div>
                                                    @endif
                                                    <div class="card-body p-2">
                                                        <p class="card-text" style="font-size: 0.7rem;">
                                                            {{ Str::limit($attachment->filename, 12) }}</p>
                                                        <a href="{{ Storage::url($attachment->filepath) }}"
                                                            class="btn btn-sm btn-primary" target="_blank">View</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if (Auth::user()->isAdmin() || ($comment->user_id == Auth::id() && !in_array($ticket->status, ['closed'])))
                                    <form action="{{ route('ticket.comments.destroy', $comment) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this comment?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="alert alert-info">
                                No comments yet. Be the first to add a comment!
                            </div>
                        @endforelse

                        @if (Auth::user()->isAdmin() || !in_array($ticket->status, ['closed', 'rejected']))
                            <div class="mt-4">
                                <h5>Add Comment</h5>
                                <form action="{{ route('ticket.comments.store', $ticket) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" rows="3"
                                            required>{{ old('comment') }}</textarea>
                                        @error('comment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="attachments" class="form-label">Attachments (Optional)</label>
                                        <input type="file"
                                            class="form-control @error('attachments.*') is-invalid @enderror"
                                            id="attachments" name="attachments[]" multiple>
                                        <div class="form-text">You can upload up to 4 attachments (JPG, PNG, PDF, DOC,
                                            DOCX, max 2MB each).</div>
                                        @error('attachments.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary">Submit Comment</button>
                                </form>
                            </div>
                        @else
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-lock me-2"></i> This ticket is closed. Only administrators can add or
                                modify comments.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Ticket Information Sidebar -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-1"></i>
                        Ticket Information
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Ticket ID:</strong>
                                <span>{{ $ticket->ticket_id }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Status:</strong>
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
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Category:</strong>
                                <span>{{ $ticket->category->name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Created By:</strong>
                                <span>{{ $ticket->user->name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Created On:</strong>
                                <span>{{ $ticket->created_at->format('M d, Y H:i') }}</span>
                            </li>
                            @if ($ticket->assigned_to)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Assigned To:</strong>
                                    <span>{{ $ticket->assignedTo->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Assigned By:</strong>
                                    <span>{{ $ticket->assignedBy->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Assigned On:</strong>
                                    <span>{{ $ticket->assigned_at->format('M d, Y H:i') }}</span>
                                </li>
                            @endif
                            @if ($ticket->resolved_at)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Resolved On:</strong>
                                    <span>{{ $ticket->resolved_at->format('M d, Y H:i') }}</span>
                                </li>
                            @endif
                            @if ($ticket->closed_at)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Closed On:</strong>
                                    <span>{{ $ticket->closed_at->format('M d, Y H:i') }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Actions Sidebar -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-cogs me-1"></i>
                        Actions
                    </div>
                    <div class="card-body">
                        @if (Auth::user()->isAdmin() && $ticket->status == 'open')
                            <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST" class="mb-3">
                                @csrf
                                <div class="mb-3">
                                    <label for="assigned_to" class="form-label">Assign Ticket</label>
                                    <select class="form-select @error('assigned_to') is-invalid @enderror"
                                        id="assigned_to" name="assigned_to" required>
                                        <option value="">Select a staff member</option>
                                        @foreach ($supportStaff as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->name }}
                                                ({{ $staff->role == 'it_support' ? 'IT Support' : 'GA Support' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Assign Ticket</button>
                                </div>
                            </form>
                        @endif

                        @if (Auth::user()->isAdmin() ||
                                ($ticket->assigned_to == Auth::id() &&
                                    !in_array($ticket->status, ['closed']) &&
                                    in_array($ticket->status, ['assigned', 'in_progress'])))
                            <form action="{{ route('tickets.update-status', $ticket) }}" method="POST" class="mb-3">
                                @csrf
                                <div class="mb-3">
                                    <label for="status" class="form-label">Update Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status"
                                        name="status" required>
                                        <option value="">Select status</option>
                                        @if ($ticket->status == 'assigned')
                                            <option value="in_progress">In Progress</option>
                                        @endif
                                        @if (in_array($ticket->status, ['assigned', 'in_progress']))
                                            <option value="resolved">Resolved</option>
                                            <option value="rejected">Rejected</option>
                                        @endif
                                        @if ($ticket->status == 'resolved')
                                            <option value="closed">Closed</option>
                                        @endif
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="rejection_reason_group" class="mb-3 d-none">
                                    <label for="rejection_reason" class="form-label">Rejection Reason</label>
                                    <textarea class="form-control @error('rejection_reason') is-invalid @enderror" id="rejection_reason"
                                        name="rejection_reason" rows="3">{{ old('rejection_reason') }}</textarea>
                                    @error('rejection_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success">Update Status</button>
                                </div>
                            </form>
                        @endif

                        @if ($ticket->status == 'resolved' && $ticket->user_id == Auth::id())
                            <form action="{{ route('tickets.update-status', $ticket) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="closed">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-secondary">Mark as Closed</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const statusSelect = document.getElementById('status');
                const rejectionReasonGroup = document.getElementById('rejection_reason_group');

                if (statusSelect && rejectionReasonGroup) {
                    statusSelect.addEventListener('change', function() {
                        if (this.value === 'rejected') {
                            rejectionReasonGroup.classList.remove('d-none');
                        } else {
                            rejectionReasonGroup.classList.add('d-none');
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
