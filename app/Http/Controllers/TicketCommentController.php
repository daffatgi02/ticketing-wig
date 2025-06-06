<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketCommentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048'
        ]);

        $user = Auth::user();

        // Check permissions to comment on the ticket
        if (!$user->isAdmin() && $ticket->assigned_to != $user->id && $ticket->user_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if ticket is closed - only admin can comment on closed tickets
        if (in_array($ticket->status, ['closed', 'rejected']) && !$user->isAdmin()) {
            return back()->with('error', 'Only administrators can comment on closed or rejected tickets.');
        }

        // Create comment (removed is_private)
        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'comment' => $request->comment
        ]);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('attachments', $filename, 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'comment_id' => $comment->id,
                    'filename' => $file->getClientOriginalName(),
                    'filepath' => $filepath,
                    'filetype' => $file->getClientMimeType(),
                    'filesize' => $file->getSize()
                ]);
            }
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Comment added successfully.');
    }

    public function destroy(TicketComment $comment)
    {
        $user = Auth::user();
        $ticket = $comment->ticket;

        // Check permissions to delete comment
        if (!$user->isAdmin() && $comment->user_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Only admin can delete comments on closed tickets
        if (in_array($ticket->status, ['closed']) && !$user->isAdmin()) {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'Only administrators can delete comments on closed tickets.');
        }

        // Delete attachments
        foreach ($comment->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->filepath);
            $attachment->delete();
        }

        $comment->delete();

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Comment deleted successfully.');
    }
}
