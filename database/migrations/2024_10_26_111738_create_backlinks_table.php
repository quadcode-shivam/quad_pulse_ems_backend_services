<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBacklinksTable extends Migration
{
    public function up()
    {
        Schema::create('backlinks', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('url');
            $table->string('website');
            $table->string('anchor_text');
            $table->integer('status')->default(1); // 1 = Pending, 2 = Approved, 3 = Rejected
            $table->text('comments')->nullable();
            $table->date('date')->default(now());
            $table->boolean('completed')->default(false);
            $table->boolean('checked')->default(false); // Add checked column
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('backlinks');
    }
}
