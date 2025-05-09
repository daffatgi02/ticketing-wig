<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'comment_id',
        'filename',
        'filepath',
        'filetype',
        'filesize',
        'use_in_report',
        'report_order'
    ];

    protected $casts = [
        'use_in_report' => 'boolean',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function comment()
    {
        return $this->belongsTo(TicketComment::class);
    }

    public function isImage()
    {
        return in_array($this->filetype, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']);
    }
}
