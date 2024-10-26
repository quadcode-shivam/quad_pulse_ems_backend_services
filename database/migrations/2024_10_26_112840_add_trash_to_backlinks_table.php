<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrashToBacklinksTable extends Migration
{
    public function up()
    {
        Schema::table('backlinks', function (Blueprint $table) {
            $table->boolean('trash')->default(0); // Default to 0 (not in trash)
        });
    }

    public function down()
    {
        Schema::table('backlinks', function (Blueprint $table) {
            $table->dropColumn('trash'); // Remove the trash column if the migration is rolled back
        });
    }
}
