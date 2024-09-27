<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('date')->notNullable();
            $table->enum('status', ['present', 'absent', 'late']);
            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
