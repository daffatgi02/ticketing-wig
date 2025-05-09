<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'content',
        'thumbnail',
        'is_default',
        'created_by'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
