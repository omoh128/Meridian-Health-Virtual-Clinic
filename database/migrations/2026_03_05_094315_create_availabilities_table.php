<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('day_of_week')->comment('0=Sun,1=Mon,...,6=Sat');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration')->default(60)->comment('minutes per slot');
            $table->integer('max_per_slot')->default(1)->comment('max bookings per slot');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Blocked dates (holidays / manual blocks)
        Schema::create('blocked_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_dates');
        Schema::dropIfExists('availabilities');
    }
};