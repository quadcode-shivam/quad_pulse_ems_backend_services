<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id(); // Automatically creates an auto-incrementing ID column
            $table->string('employee_id'); // Foreign key for employee
            $table->string('leave_type'); // Type of leave
            $table->string('start_date'); // Start date of leave
            $table->string('end_date'); // End date of leave
            $table->string('status')->default('pending'); // Leave status
            $table->text('description')->nullable(); // Optional description
            $table->enum('half_day_full_day', ['half', 'full']); // Half day or full day
            $table->timestamps(); // Creates created_at and updated_at columns

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leaves'); // Drop the table if it exists
    }
}
