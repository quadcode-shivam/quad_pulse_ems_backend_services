<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskManagementTables extends Migration
{
    public function up()
    {
        // Tasks Table
        Schema::create('tasks', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('employee_id')->nullable(); // Employee ID without foreign key constraint
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', [
                'todo', 
                'in-progress', 
                'ready-for-staging', 
                'staging', 
                'ready-for-production', 
                'production', 
                'done', 
                'block'
            ])->default('todo');
            $table->integer('priority')->default(1); // 1 - Low, 2 - Medium, 3 - High
            $table->timestamp('due_date')->nullable();
            $table->timestamps();
        });

        // Task Comments Table
        Schema::create('task_comments', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('task_id')->nullable(); // Task ID without foreign key constraint
            $table->string('employee_id')->nullable(); // Employee ID without foreign key constraint
            $table->text('comment');
            $table->timestamps();
        });

        // Task History Table
        Schema::create('task_history', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('task_id')->nullable(); // Task ID without foreign key constraint
            $table->string('employee_id')->nullable(); // Employee ID without foreign key constraint
            $table->string('action'); // E.g., 'status update', 'priority change', etc.
            $table->json('previous_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamps();
        });

        // Task Attachments Table
        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('task_id')->nullable(); // Task ID without foreign key constraint
            $table->string('file_name');
            $table->string('file_path');
            $table->timestamps();
        });

        // Sprints Table (Optional)
        Schema::create('sprints', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name');
            $table->text('goal')->nullable();
            $table->timestamp('start_date')->nullable(); // Make nullable
            $table->timestamp('end_date')->nullable(); // Allow null
            $table->timestamps();
        });

        // Task Assignments Table (Optional)
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('task_id')->nullable(); // Task ID without foreign key constraint
            $table->string('employee_id')->nullable(); // Employee ID without foreign key constraint
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_assignments');
        Schema::dropIfExists('sprints');
        Schema::dropIfExists('task_attachments');
        Schema::dropIfExists('task_history');
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('tasks');
    }
}
