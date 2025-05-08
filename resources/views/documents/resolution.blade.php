<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Resolution Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 5px;
        }
        .ticket-info {
            margin-bottom: 20px;
        }
        .ticket-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .ticket-info table td {
            padding: 5px;
        }
        .ticket-info table td:first-child {
            font-weight: bold;
            width: 180px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            background-color: #f5f5f5;
            padding: 5px 10px;
            border-left: 4px solid #0066cc;
        }
        .comment {
            border-left: 3px solid #ccc;
            padding-left: 10px;
            margin-bottom: 15px;
        }
        .comment-header {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .content {
            text-align: justify;
        }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Ticket Resolution Report</h1>
        <p>Ticket ID: {{ $ticket->ticket_id }}</p>
    </div>

    <div class="ticket-info">
        <table>
            <tr>
                <td>Title:</td>
                <td>{{ $ticket->title }}</td>
            </tr>
            <tr>
                <td>Category:</td>
                <td>{{ $ticket->category->name }}</td>
            </tr>
            <tr>
                <td>Status:</td>
                <td>{{ ucfirst($ticket->status) }}</td>
            </tr>
            <tr>
                <td>Reported By:</td>
                <td>{{ $ticket->user->name }}</td>
            </tr>
            <tr>
                <td>Department:</td>
                <td>{{ $ticket->user->department ? $ticket->user->department->name : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Created:</td>
                <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td>Assigned To:</td>
                <td>{{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Not Assigned' }}</td>
            </tr>
            <tr>
                <td>Resolved At:</td>
                <td>{{ $ticket->resolved_at ? $ticket->resolved_at->format('Y-m-d H:i') : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Closed At:</td>
                <td>{{ $ticket->closed_at ? $ticket->closed_at->format('Y-m-d H:i') : 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Description</h2>
        <div class="content">
            {{ $ticket->description }}
        </div>
    </div>

    <div class="section">
        <h2>Resolution</h2>
        <div class="content">
            @php
                $resolutionComment = $ticket->comments->where('user_id', $ticket->assigned_to)->last();
            @endphp

            @if($resolutionComment)
                {{ $resolutionComment->comment }}
            @else
                No resolution comment provided.
            @endif
        </div>
    </div>

    <div class="section">
        <h2>Conversation History</h2>

        @foreach($ticket->comments as $comment)
            <div class="comment">
                <div class="comment-header">
                    {{ $comment->user->name }}
                    ({{ $comment->user->role == 'admin' ? 'Administrator' :
                       ($comment->user->role == 'it_support' ? 'IT Support' :
                       ($comment->user->role == 'ga_support' ? 'GA Support' : 'User')) }})
                    - {{ $comment->created_at->format('Y-m-d H:i') }}
                </div>
                <div class="content">
                    {{ $comment->comment }}
                </div>
            </div>
        @endforeach
    </div>

    @if(count($ticket->attachments->where('comment_id', null)) > 0)
    <div class="section">
        <h2>Attachments</h2>
        <ul>
            @foreach($ticket->attachments->where('comment_id', null) as $attachment)
                <li>{{ $attachment->filename }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="footer">
        This document was automatically generated on {{ now()->format('Y-m-d H:i') }}
    </div>
</body>
</html>
