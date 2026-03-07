<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->string('alias')->nullable();
            $table->enum('ssl_status', ['None', 'Invalid', 'Valid', 'disabled'])->default('None');
            $table->enum('status', ['Up', 'Down', 'disabled'])->default('Up');
            $table->decimal('uptime_percentage', 5, 2)->default(100.00);
            $table->decimal('response_time', 8, 4)->nullable(); // e.g. 0.1429
            $table->timestamp('last_checked_at')->nullable();
            $table->boolean('is_active')->default(true); // mapped to pause/play state
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
