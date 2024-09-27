<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); // Keeping as a string, no foreign key
            $table->date('attendance_date');
            $table->time('check_in_time')->nullable();
            $table->string('check_in_description')->nullable(); // Description for check-in
            $table->time('check_out_time')->nullable();
            $table->string('check_out_description')->nullable(); // Description for check-out
            $table->string('status')->default('present'); // e.g., present, absent, leave
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
}
