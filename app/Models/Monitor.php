<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    /** @use HasFactory<\Database\Factories\MonitorFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'url',
        'alias',
        'ssl_status',
        'status',
        'uptime_percentage',
        'response_time',
        'last_checked_at',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'uptime_percentage' => 'decimal:2',
        'response_time' => 'decimal:4',
        'last_checked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
