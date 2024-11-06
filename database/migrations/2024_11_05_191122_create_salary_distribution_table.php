<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryDistributionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_distributions', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('user_id'); // Foreign key to users table
            $table->decimal('amount', 10, 2); // Salary amount
            $table->string('status'); // e.g., "paid", "pending", "failed"
            $table->string('salary_month'); // e.g., "2024-11"
            $table->date('transaction_date'); // Date of the transaction
            $table->string('transaction_id')->unique(); // Unique identifier for the transaction
            $table->string('payment_method')->nullable(); // Payment method (e.g., "bank transfer", "check")
            $table->string('currency', 3)->default('USD'); // Currency (default is USD)
            $table->text('notes')->nullable(); // Additional notes or remarks
            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_distributions');
    }
}
