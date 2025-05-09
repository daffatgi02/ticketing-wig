<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use App\Models\DocTemplate;
use App\Services\DocumentGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->status;
        $needsExternalSupport = $request->needs_external_support;

        // Base query
        $query = Ticket::query();

        // Filter berdasarkan role
        if ($user->isAdmin()) {
            // Admin sees all tickets
            $query->with(['user', 'category', 'assignedTo']);
        } elseif ($user->isSupport()) {
            // Support staff can only see tickets assigned to them
            $query->with(['user', 'category'])
                ->where('assigned_to', $user->id);
        } else {
            // Regular users see only their own tickets
            $query->with(['category', 'assignedTo'])
                ->where('user_id', $user->id);
        }

        // Filter berdasarkan status
        if ($status && $status != 'all') {
            $query->where('status', $status);
        }

        // Filter berdasarkan kebutuhan external support
        if ($needsExternalSupport) {
            $query->where('needs_external_support', true);
        }

        // Get result
        $tickets = $query->latest()->get();

        return view('tickets.index', compact('tickets'));
    }


    public function create()
    {
        $categories = Category::all();
        return view('tickets.create', compact('categories'));
    }

    public function markNeedsExternalSupport(Request $request, Ticket $ticket)
    {
        $request->validate([
            'external_support_reason' => 'required|string',
            'document_format' => 'required|in:pdf,docx'
        ]);

        $user = Auth::user();

        // Verify user is support staff assigned to this ticket or admin
        if (!$user->isAdmin() && $ticket->assigned_to != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Only support staff can mark tickets as needing external support
        if (!$user->isSupport() && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Generate BAK and RKB documents
            $documentGenerator = new DocumentGenerator();
            $format = $request->document_format;

            $bakPath = $documentGenerator->generateBAKDocument($ticket, $format);
            $rkbPath = $documentGenerator->generateRKBDocument($ticket, $format);

            $ticket->update([
                'needs_external_support' => true,
                'external_support_reason' => $request->external_support_reason,
                'bak_document' => $bakPath,
                'rkb_document' => $rkbPath,
                'external_support_requested_at' => now()
            ]);

            return redirect()->route('tickets.show', $ticket)
                ->with('success', 'Ticket marked as needing external support. BAK and RKB documents have been generated.');
        } catch (\Exception $e) {
            // Log error
            \Log::error('Failed to generate support documents: ' . $e->getMessage());

            // Tetap update status tiket meskipun dokumen gagal dibuat
            $ticket->update([
                'needs_external_support' => true,
                'external_support_reason' => $request->external_support_reason,
                'external_support_requested_at' => now()
            ]);

            return redirect()->route('tickets.show', $ticket)
                ->with('warning', 'Ticket marked as needing external support, but failed to generate documents.');
        }
    }
    public function previewDocument(Request $request, Ticket $ticket)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        return response()->json([
            'html' => $request->content
        ]);
    }
    public function documentEditor(Ticket $ticket, $type)
    {
        $user = Auth::user();

        // Verify user is support staff assigned to this ticket or admin
        if (!$user->isAdmin() && $ticket->assigned_to != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Only support staff can access document editor
        if (!$user->isSupport() && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Validate document type
        if (!in_array($type, ['bak', 'rkb'])) {
            abort(404, 'Invalid document type.');
        }

        // Load template
        $template = DocTemplate::where('type', $type)
            ->where('is_default', true)
            ->first();

        // If no template exists, create a basic one
        if (!$template) {
            if ($type == 'bak') {
                $content = view('templates.default.bak', compact('ticket'))->render();
            } else {
                $content = view('templates.default.rkb', compact('ticket'))->render();
            }
        } else {
            $content = $template->content;

            // Replace placeholders with ticket data
            $content = str_replace('{ticket_id}', $ticket->ticket_id, $content);
            $content = str_replace('{issue_detail}', $ticket->issue_detail ?? $ticket->description, $content);
            $content = str_replace('{actions_taken}', $ticket->actions_taken ?? '', $content);
            $content = str_replace('{report_recipient}', $ticket->report_recipient ?? 'Bpk Agus', $content);
            $content = str_replace('{report_recipient_position}', $ticket->report_recipient_position ?? 'General Affair', $content);
            $content = str_replace('{today_date}', now()->format('d F Y'), $content);
            $content = str_replace('{incident_date}', $ticket->incident_date ? $ticket->incident_date->format('l, d F Y') : now()->format('l, d F Y'), $content);
            $content = str_replace('{incident_time}', $ticket->incident_time ? $ticket->incident_time->format('H:i') : now()->format('H:i'), $content);
            $content = str_replace('{created_by}', $user->name, $content);
            $content = str_replace('{department}', $user->department ? $user->department->name : 'Staff IT', $content);
        }

        // Get document images
        $reportImages = $ticket->attachments()
            ->where('use_in_report', true)
            ->orderBy('report_order')
            ->get();

        return view('tickets.document_editor', compact('ticket', 'type', 'content', 'reportImages'));
    }

    public function saveDocument(Request $request, Ticket $ticket)
    {
        $request->validate([
            'type' => 'required|in:bak,rkb',
            'content' => 'required|string'
        ]);

        $user = Auth::user();

        // Verify user is support staff assigned to this ticket or admin
        if (!$user->isAdmin() && $ticket->assigned_to != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Save document content to session for later use
        session([$request->type . '_document_' . $ticket->id => $request->content]);

        return response()->json([
            'success' => true,
            'message' => 'Document saved successfully.'
        ]);
    }

    public function publishDocument(Request $request, Ticket $ticket)
    {
        $request->validate([
            'type' => 'required|in:bak,rkb',
            'content' => 'required|string',
            'format' => 'required|in:pdf,docx'
        ]);

        $user = Auth::user();

        // Verify user is support staff assigned to this ticket or admin
        if (!$user->isAdmin() && $ticket->assigned_to != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Use custom content for document generation
            $documentGenerator = new DocumentGenerator();
            $documentPath = null;

            if ($request->type == 'bak') {
                $documentPath = $documentGenerator->generateCustomBAKDocument($ticket, $request->content, $request->format);
                $ticket->update(['bak_document' => $documentPath]);
            } else {
                $documentPath = $documentGenerator->generateCustomRKBDocument($ticket, $request->content, $request->format);
                $ticket->update(['rkb_document' => $documentPath]);
            }

            // Clear session content
            session()->forget($request->type . '_document_' . $ticket->id);

            return response()->json([
                'success' => true,
                'message' => 'Document published successfully.',
                'redirect' => route('tickets.show', $ticket)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish document: ' . $e->getMessage()
            ], 500);
        }
    }

    // Tambahkan metode untuk download dokumen
    public function downloadDocument(Ticket $ticket, $documentType)
    {
        $user = Auth::user();

        // Check if user has permission to download document
        if (!$user->isAdmin() && !$user->isSupport() && $ticket->user_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $filePath = null;
        $fileName = null;

        switch ($documentType) {
            case 'resolution':
                if (!$ticket->resolution_document) {
                    return back()->with('error', 'Resolution document not available.');
                }
                $filePath = $ticket->resolution_document;
                $fileName = 'Resolution_' . $ticket->ticket_id . '.pdf';
                break;

            case 'bak':
                if (!$ticket->bak_document) {
                    return back()->with('error', 'BAK document not available.');
                }
                $filePath = $ticket->bak_document;
                $fileName = 'BAK_' . $ticket->ticket_id . '.' . pathinfo($ticket->bak_document, PATHINFO_EXTENSION);
                break;

            case 'rkb':
                if (!$ticket->rkb_document) {
                    return back()->with('error', 'RKB document not available.');
                }
                $filePath = $ticket->rkb_document;
                $fileName = 'RKB_' . $ticket->ticket_id . '.' . pathinfo($ticket->rkb_document, PATHINFO_EXTENSION);
                break;

            default:
                return back()->with('error', 'Invalid document type.');
        }

        if (!Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'Document file not found.');
        }

        return Storage::disk('public')->download($filePath, $fileName);
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
            'status' => 'open' // All new tickets start as 'open' and await admin assignment
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
            ->with('success', 'Ticket created successfully and awaiting review by administrator.');
    }

    // Method untuk menampilkan form external support
    public function showExternalSupportForm(Ticket $ticket)
    {
        $user = Auth::user();

        // Verify user is support staff assigned to this ticket or admin
        if (!$user->isAdmin() && $ticket->assigned_to != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Only support staff can access this form
        if (!$user->isSupport() && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('tickets.external_support_form', compact('ticket'));
    }


    // Method untuk memproses form dan membuat dokumen
    public function submitExternalSupport(Request $request, Ticket $ticket)
    {
        $request->validate([
            'incident_date' => 'required|date',
            'incident_time' => 'required',
            'issue_detail' => 'required|string',
            'actions_taken' => 'required|string',
            'external_support_reason' => 'required|string',
            'report_recipient' => 'required|string|max:255',
            'report_recipient_position' => 'required|string|max:255',
            'additional_notes' => 'nullable|string',
            'document_format' => 'required|in:pdf,docx',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048',
            'report_images.*' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'attachment_order.*' => 'nullable|integer|min:1|max:10',
            'remove_attachments.*' => 'nullable|exists:ticket_attachments,id'
        ]);

        $user = Auth::user();

        // Verify user is support staff assigned to this ticket or admin
        if (!$user->isAdmin() && $ticket->assigned_to != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Only support staff can mark tickets as needing external support
        if (!$user->isSupport() && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Update ticket details
            $ticket->update([
                'incident_date' => $request->incident_date,
                'incident_time' => $request->incident_time,
                'issue_detail' => $request->issue_detail,
                'actions_taken' => $request->actions_taken,
                'external_support_reason' => $request->external_support_reason,
                'report_recipient' => $request->report_recipient,
                'report_recipient_position' => $request->report_recipient_position,
                'additional_notes' => $request->additional_notes,
                'needs_external_support' => true,
                'external_support_requested_at' => $ticket->external_support_requested_at ?? now()
            ]);

            // Process removed attachments
            if ($request->has('remove_attachments')) {
                foreach ($request->remove_attachments as $attachmentId) {
                    $attachment = TicketAttachment::find($attachmentId);

                    if ($attachment && $attachment->ticket_id == $ticket->id) {
                        // Just set use_in_report to false, don't delete the file
                        $attachment->update([
                            'use_in_report' => false,
                            'report_order' => null
                        ]);
                    }
                }
            }

            // Update ordering for existing report attachments
            if ($request->has('attachment_order')) {
                foreach ($request->attachment_order as $attachmentId => $order) {
                    $attachment = TicketAttachment::find($attachmentId);

                    if ($attachment && $attachment->ticket_id == $ticket->id) {
                        $attachment->update([
                            'report_order' => $order
                        ]);
                    }
                }
            }

            // Handle report image uploads
            if ($request->hasFile('report_images')) {
                // Count existing report images
                $existingCount = $ticket->attachments()->where('use_in_report', true)->count();
                $maxNewImages = 10 - $existingCount;
                $newImages = array_slice($request->file('report_images'), 0, $maxNewImages);

                // Get the highest current order
                $highestOrder = $ticket->attachments()->where('use_in_report', true)->max('report_order') ?? 0;

                foreach ($newImages as $index => $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = $file->storeAs('attachments', $filename, 'public');

                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'filename' => $file->getClientOriginalName(),
                        'filepath' => $filepath,
                        'filetype' => $file->getClientMimeType(),
                        'filesize' => $file->getSize(),
                        'use_in_report' => true,
                        'report_order' => $highestOrder + $index + 1
                    ]);
                }
            }

            // Handle other file uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $filepath = $file->storeAs('attachments', $filename, 'public');

                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'filename' => $file->getClientOriginalName(),
                        'filepath' => $filepath,
                        'filetype' => $file->getClientMimeType(),
                        'filesize' => $file->getSize(),
                        'use_in_report' => false
                    ]);
                }
            }

            // Generate BAK and RKB documents
            $documentGenerator = new DocumentGenerator();
            $format = $request->document_format;

            $bakPath = $documentGenerator->generateBAKDocument($ticket, $format);
            $rkbPath = $documentGenerator->generateRKBDocument($ticket, $format);

            $ticket->update([
                'bak_document' => $bakPath,
                'rkb_document' => $rkbPath
            ]);

            // Determine message based on whether this is an update or initial creation
            $message = $ticket->external_support_requested_at && $ticket->external_support_requested_at->lt(now()->subMinutes(5))
                ? 'External support reports updated successfully.'
                : 'External support reports generated successfully.';

            return redirect()->route('tickets.show', $ticket)
                ->with('success', $message);
        } catch (\Exception $e) {
            // Log error
            \Log::error('Failed to generate support documents: ' . $e->getMessage());

            // Update ticket status but warn about document failure
            $ticket->update([
                'incident_date' => $request->incident_date,
                'incident_time' => $request->incident_time,
                'issue_detail' => $request->issue_detail,
                'actions_taken' => $request->actions_taken,
                'external_support_reason' => $request->external_support_reason,
                'report_recipient' => $request->report_recipient,
                'report_recipient_position' => $request->report_recipient_position,
                'additional_notes' => $request->additional_notes,
                'needs_external_support' => true,
                'external_support_requested_at' => $ticket->external_support_requested_at ?? now()
            ]);

            return redirect()->route('tickets.show', $ticket)
                ->with('warning', 'Ticket updated with external support details, but failed to generate documents. Error: ' . $e->getMessage());
        }
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
            'rejection_reason' => 'required_if:status,rejected',
            'needs_external_support' => 'sometimes|boolean',
            'external_support_reason' => 'required_if:needs_external_support,1',
        ]);

        $user = Auth::user();

        // Special case: Anyone can close a resolved or rejected ticket
        if ($request->status === 'closed' && in_array($ticket->status, ['resolved', 'rejected'])) {
            try {
                // Generate resolution document when closing a ticket
                $documentGenerator = app()->make('document.generator'); // Gunakan dependency injection
                // Atau langsung buat instance baru:
                // $documentGenerator = new DocumentGenerator();
                $documentPath = $documentGenerator->generateResolutionDocument($ticket, 'pdf');

                $ticket->update([
                    'status' => 'closed',
                    'closed_at' => now(),
                    'resolution_document' => $documentPath
                ]);

                return redirect()->route('tickets.show', $ticket)
                    ->with('success', 'Ticket closed successfully and resolution document generated.');
            } catch (\Exception $e) {
                // Log error
                \Log::error('Failed to generate resolution document: ' . $e->getMessage());

                // Tetap update status tiket meskipun dokumen gagal dibuat
                $ticket->update([
                    'status' => 'closed',
                    'closed_at' => now()
                ]);

                return redirect()->route('tickets.show', $ticket)
                    ->with('warning', 'Ticket closed successfully but failed to generate resolution document.');
            }
        }
        // For other status changes, check permissions
        if (!$user->isAdmin() && $ticket->assigned_to != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Only admin can update closed tickets
        if (in_array($ticket->status, ['closed']) && !$user->isAdmin()) {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'Only administrators can modify closed tickets.');
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

            // Generate resolution document
            $documentGenerator = new DocumentGenerator();
            $documentPath = $documentGenerator->generateResolutionDocument($ticket, 'pdf');
            $data['resolution_document'] = $documentPath;
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
