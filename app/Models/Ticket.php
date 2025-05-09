<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'category_id',
        'title',
        'description',
        'status',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'resolved_at',
        'closed_at',
        'rejection_reason',
        'needs_external_support',
        'external_support_reason',
        'bak_document',
        'rkb_document',
        'resolution_document',
        'external_support_requested_at',
        'incident_date',
        'incident_time',
        'issue_detail',
        'actions_taken',
        'report_recipient',
        'report_recipient_position',
        'additional_notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'external_support_requested_at' => 'datetime',
        'needs_external_support' => 'boolean',
        'incident_date' => 'date',
        'incident_time' => 'datetime',
    ];
    // Relasi yang sudah ada
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }
}
