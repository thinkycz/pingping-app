<?php

namespace Tests\Feature;

use App\Models\Monitor;
use App\Models\PingLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MonitoringDataMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitoring_field_migration_round_trip_preserves_existing_rows(): void
    {
        $monitor = Monitor::factory()->create([
            'uptime_30d' => 98.75,
            'response_time_ms' => 321,
        ]);
        PingLog::query()->create([
            'monitor_id' => $monitor->id,
            'status' => 'Up',
            'response_time_ms' => 287,
            'ssl_status' => 'Valid',
        ]);
        $migration = require database_path('migrations/2026_08_24_000000_harden_monitoring_data.php');

        $migration->down();

        $this->assertTrue(Schema::hasColumn('monitors', 'uptime_percentage'));
        $this->assertSame(98.75, (float) DB::table('monitors')->value('uptime_percentage'));
        $this->assertSame(287.0, (float) DB::table('ping_logs')->value('response_time'));

        $migration->up();

        $this->assertDatabaseCount('monitors', 1);
        $this->assertDatabaseCount('ping_logs', 1);
        $this->assertSame(98.75, (float) DB::table('monitors')->value('uptime_30d'));
        $this->assertSame(287, DB::table('ping_logs')->value('response_time_ms'));
    }
}
