<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_policies', function (Blueprint $table) {
            // Remove the leave_type column
            $table->dropColumn('leave_type');
            
            // Add new columns
            $table->integer('total_leave')->default(0);
            $table->integer('total_half_day')->default(0);
            $table->integer('total_full_day')->default(0);
            $table->integer('total_late')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('leave_policies', function (Blueprint $table) {
            // Add the leave_type column back
            $table->enum('leave_type', ['sick', 'vacation', 'personal', 'other'])->after('id');
            
            // Drop the newly added columns
            $table->dropColumn(['total_leave', 'total_half_day', 'total_full_day', 'total_late']);
        });
    }
};
