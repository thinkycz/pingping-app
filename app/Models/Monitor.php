<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
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
        'interval',
        'ssl_expiration_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'uptime_percentage' => 'decimal:2',
        'response_time' => 'decimal:4',
        'last_checked_at' => 'datetime',
        'ssl_expiration_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pingLogs()
    {
        return $this->hasMany(PingLog::class);
    }
}
