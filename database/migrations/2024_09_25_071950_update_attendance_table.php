<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAttendanceTable extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Adding a new column for attendance notes
            $table->string('notes')->nullable()->after('date'); // Make sure this line is correct

            // If you need to change the existing status column, use the following lines
            // $table->dropColumn('status'); // Drop the existing status column (only if you need to change it)
            // $table->string('status')->default('present')->after('date'); // Re-add the status column if needed
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Remove the notes column if it exists
            $table->dropColumn('notes');

            // If you previously dropped the status column, restore it
            // $table->enum('status', ['present', 'absent', 'late'])->after('date'); // Restore the enum column
        });
    }
}
