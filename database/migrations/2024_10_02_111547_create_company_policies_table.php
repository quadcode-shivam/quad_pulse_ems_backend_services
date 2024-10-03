<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyPoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_policies', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('title'); // Title of the policy
            $table->text('description'); // Detailed description of the policy
            $table->string('category')->nullable(); // Category of the policy (e.g., HR, Safety, etc.)
            $table->boolean('is_active')->default(true); // Status of the policy
            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_policies');
    }
}
