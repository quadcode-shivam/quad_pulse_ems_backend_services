<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckinsTable extends Migration
{
    public function up()
    {
        Schema::create('checkins', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('employee_id')->nullable(); // Employee ID as varchar
            $table->string('status')->nullable(); // Status field
            $table->string('user_name')->nullable(); // User's name
            $table->string('email')->nullable(); // User's email
            $table->string('role')->nullable(); // User's role
            $table->timestamp('check_in_time')->nullable(); // Check-in time
            $table->timestamp('check_out_time')->nullable(); // Check-out time
            $table->string('check_in_info')->nullable(); // Optional additional info for check-in
            $table->string('check_out_info')->nullable(); // Optional additional info for check-out
            $table->timestamps(); // Created at and updated at timestamps

            // Foreign key constraint
        });
    }

    public function down()
    {
        Schema::dropIfExists('checkins');
    }
}
