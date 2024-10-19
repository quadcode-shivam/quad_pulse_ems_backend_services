<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndHolidayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ind_holiday', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('title'); // Holiday title
            $table->date('start_date'); // Start date of the holiday
            $table->date('end_date'); // End date of the holiday
            $table->text('description')->nullable(); // Holiday description
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ind_holiday');
    }
}
