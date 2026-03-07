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
        'response_time',
        'ssl_status',
    ];

    protected $casts = [
        'response_time' => 'decimal:4',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
