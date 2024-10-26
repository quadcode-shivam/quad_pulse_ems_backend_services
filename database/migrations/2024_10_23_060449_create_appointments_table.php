<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); 
            $table->string('name'); 
            $table->string('email')->unique();
            $table->string('reason'); 
            $table->date('date'); 
            $table->time('time');
            $table->string('status')
                  ->default('Pending')
                  ->comment('Status of the appointment: 1) Pending, 2) Accepted, 3) Suspended. Default is Pending.'); 
            $table->timestamps(); 

            $table->comment('Stores appointment details including name, email, reason, date, time, and status.');
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
