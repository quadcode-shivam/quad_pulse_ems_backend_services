<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToBacklinksTable extends Migration
{
    public function up()
    {
        Schema::table('backlinks', function (Blueprint $table) {
            $table->boolean('user_id')->default(0); // Default to 0 (not in user_id)
        });
    }

    public function down()
    {
        Schema::table('backlinks', function (Blueprint $table) {
            $table->dropColumn('user_id'); // Remove the user_id column if the migration is rolled back
        });
    }
}