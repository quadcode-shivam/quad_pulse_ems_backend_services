<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Alter the status column to add new values
        Schema::table('attendances', function (Blueprint $table) {
            // Modify the column to add new enum values
            $table->enum('status', ['present', 'absent', 'late', 'halfday', 'fullday'])
                  ->default('present') // Set a default value if necessary
                  ->change();
        });
    }

    public function down(): void
    {
        // Roll back the changes by resetting the status column
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['present', 'absent', 'late']) // Remove the new values
                  ->default('present') // Set the default if necessary
                  ->change();
        });
    }
};
