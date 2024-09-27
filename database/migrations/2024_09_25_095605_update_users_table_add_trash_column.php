<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adding the trash column
            $table->boolean('trash')->default(0); // or use $table->softDeletes(); for soft delete functionality

            // Example of modifying an existing column (e.g., changing the 'role' column)
            // $table->enum('role', ['admin', 'employee', 'manager'])->change(); // Add 'manager' role
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Dropping the trash column
            $table->dropColumn('trash');

            // If you modified an existing column, revert the change
            // $table->enum('role', ['admin', 'employee'])->change();
        });
    }
};
