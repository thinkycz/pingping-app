<?php

use App\Jobs\PingMonitorsJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new PingMonitorsJob)->everyMinute();
