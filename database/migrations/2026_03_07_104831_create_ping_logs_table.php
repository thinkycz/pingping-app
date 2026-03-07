<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ping_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitor_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['Up', 'Down', 'disabled'])->default('Up');
            $table->decimal('response_time', 8, 4)->nullable();
            $table->enum('ssl_status', ['None', 'Invalid', 'Valid', 'disabled'])->default('None');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ping_logs');
    }
};
