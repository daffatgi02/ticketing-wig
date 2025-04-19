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
            'is_private' => 'boolean',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048'
        ]);

        $user = Auth::user();

        // Check permissions to comment on the ticket
        if (!$user->isAdmin() && $ticket->assigned_to != $user->id && $ticket->user_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if ticket is closed
        if (in_array($ticket->status, ['closed', 'rejected'])) {
            return back()->with('error', 'Cannot comment on a closed or rejected ticket.');
        }

        // Create comment
        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'comment' => $request->comment,
            'is_private' => $request->is_private ?? false
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
