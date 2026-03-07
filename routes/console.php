<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\PingMonitorsJob;

Schedule::job(new PingMonitorsJob)->everyMinute();