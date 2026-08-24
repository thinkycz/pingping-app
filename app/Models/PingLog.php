<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitor_id',
        'status',
        'response_time_ms',
        'http_status',
        'failure_code',
        'failure_detail',
        'ssl_status',
    ];

    protected $casts = [
        'response_time_ms' => 'integer',
        'http_status' => 'integer',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
