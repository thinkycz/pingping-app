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
        'uptime_30d',
        'response_time_ms',
        'last_http_status',
        'failure_code',
        'failure_detail',
        'last_checked_at',
        'is_active',
        'interval',
        'ssl_expiration_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'uptime_30d' => 'decimal:2',
        'response_time_ms' => 'integer',
        'last_http_status' => 'integer',
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

    public function displayState(): string
    {
        if (! $this->is_active) {
            return 'paused';
        }

        if ($this->last_checked_at === null) {
            return 'pending';
        }

        return strtolower($this->status);
    }
}
