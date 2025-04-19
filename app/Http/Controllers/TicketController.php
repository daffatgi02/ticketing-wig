<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $tickets = Ticket::with(['user', 'category', 'assignedTo'])->latest()->get();
        } elseif ($user->isSupport()) {
            $tickets = Ticket::with(['user', 'category'])
                ->where('assigned_to', $user->id)
                ->orWhere(function($query) {
                    $query->whereNull('assigned_to')
                        ->where('status', 'open');
                })
                ->latest()
                ->get();
        } else {
            $tickets = Ticket::with(['category', 'assignedTo'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('tickets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048'
        ]);

        // Generate ticket ID (TIK-YYYYMMDD-XXXX)
        $latestTicket = Ticket::whereDate('created_at', today())->latest()->first();
        $counter = $latestTicket ? intval(substr($latestTicket->ticket_id, -4)) + 1 : 1;

        $ticketId = 'TIK-' . date('Ymd') . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);

        $ticket = Ticket::create([
            'ticket_id' => $ticketId,
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'open'
        ]);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('attachments', $filename, 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'filename' => $file->getClientOriginalName(),
                    'filepath' => $filepath,
                    'filetype' => $file->getClientMimeType(),
                    'filesize' => $file->getSize()
                ]);
            }
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket)
    {
        $user = Auth::user();

        // Check if user has permission to view this ticket
        if (!$user->isAdmin() && !$ticket->assigned_to == $user->id && $ticket->user_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $ticket->load(['user', 'category', 'assignedTo', 'assignedBy', 'comments.user', 'comments.attachments', 'attachments']);

        // Get support staff for assignment
        $supportStaff = User::whereIn('role', ['it_support', 'ga_support'])->get();

        return view('tickets.show', compact('ticket', 'supportStaff'));
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $assignedTo = User::findOrFail($request->assigned_to);

        // Check if user has permission to assign
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Only assign if ticket is in 'open' status
        if ($ticket->status != 'open') {
            return back()->with('error', 'Can only assign tickets that are in Open status.');
        }

        $ticket->update([
            'assigned_to' => $assignedTo->id,
            'assigned_by' => Auth::id(),
            'assigned_at' => now(),
            'status' => 'assigned'
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket assigned successfully.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:in_progress,resolved,closed,rejected',
            'rejection_reason' => 'required_if:status,rejected'
        ]);

        $user = Auth::user();

        // Check if user has permission to update status
        if (!$user->isAdmin() && $ticket->assigned_to != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'status' => $request->status
        ];

        if ($request->status == 'rejected') {
            $data['rejection_reason'] = $request->rejection_reason;
        }

        if ($request->status == 'resolved') {
            $data['resolved_at'] = now();
        }

        if ($request->status == 'closed') {
            $data['closed_at'] = now();
        }

        $ticket->update($data);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket status updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        // Only admin can delete tickets
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete all attachments
        foreach ($ticket->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->filepath);
            $attachment->delete();
        }

        // Delete comments and their attachments
        foreach ($ticket->comments as $comment) {
            foreach ($comment->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->filepath);
                $attachment->delete();
            }
            $comment->delete();
        }

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }
}
