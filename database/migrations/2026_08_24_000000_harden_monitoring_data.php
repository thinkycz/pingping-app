<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('locale', 2)->default('en')->after('email');
        });

        Schema::table('monitors', function (Blueprint $table) {
            $table->decimal('uptime_30d', 5, 2)->default(100)->after('status');
            $table->unsignedInteger('response_time_ms')->nullable()->after('uptime_30d');
            $table->unsignedSmallInteger('last_http_status')->nullable()->after('response_time_ms');
            $table->string('failure_code', 64)->nullable()->after('last_http_status');
            $table->string('failure_detail', 255)->nullable()->after('failure_code');
            $table->index(['user_id', 'is_active', 'status']);
        });

        Schema::table('ping_logs', function (Blueprint $table) {
            $table->unsignedInteger('response_time_ms')->nullable()->after('status');
            $table->unsignedSmallInteger('http_status')->nullable()->after('response_time_ms');
            $table->string('failure_code', 64)->nullable()->after('http_status');
            $table->string('failure_detail', 255)->nullable()->after('failure_code');
        });

        DB::table('monitors')->update([
            'uptime_30d' => DB::raw('uptime_percentage'),
            'response_time_ms' => DB::raw('ROUND(response_time)'),
        ]);
        DB::table('ping_logs')->update([
            'response_time_ms' => DB::raw('ROUND(response_time)'),
        ]);
        DB::table('monitors')->where('status', 'disabled')->update(['status' => 'Up']);
        DB::table('monitors')->where('ssl_status', 'disabled')->update(['ssl_status' => 'None']);

        Schema::table('monitors', function (Blueprint $table) {
            $table->dropColumn(['uptime_percentage', 'response_time']);
        });
        Schema::table('ping_logs', function (Blueprint $table) {
            $table->dropColumn('response_time');
        });
    }

    public function down(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->decimal('uptime_percentage', 5, 2)->default(100);
            $table->decimal('response_time', 8, 4)->nullable();
        });
        Schema::table('ping_logs', function (Blueprint $table) {
            $table->decimal('response_time', 8, 4)->nullable();
        });

        DB::table('monitors')->update([
            'uptime_percentage' => DB::raw('uptime_30d'),
            'response_time' => DB::raw('response_time_ms'),
        ]);
        DB::table('ping_logs')->update([
            'response_time' => DB::raw('response_time_ms'),
        ]);

        Schema::table('monitors', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_active', 'status']);
            $table->dropColumn([
                'uptime_30d',
                'response_time_ms',
                'last_http_status',
                'failure_code',
                'failure_detail',
            ]);
        });
        Schema::table('ping_logs', function (Blueprint $table) {
            $table->dropColumn([
                'response_time_ms',
                'http_status',
                'failure_code',
                'failure_detail',
            ]);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
