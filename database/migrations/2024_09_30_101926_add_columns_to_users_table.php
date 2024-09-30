<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('position')->nullable(); // Add position
            $table->string('designation')->nullable(); // Add designation
            $table->decimal('salary', 10, 2)->nullable(); // Add salary
            $table->date('date_hired')->nullable(); // Add date_hired
            // The created_at and updated_at columns are already handled by timestamps()
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('position');
            $table->dropColumn('designation');
            $table->dropColumn('salary');
            $table->dropColumn('date_hired');
        });
    }
}
