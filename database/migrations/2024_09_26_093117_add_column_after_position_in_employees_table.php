<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAfterPositionInEmployeesTable extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add the new column after the 'position' column
            $table->string('designation')->after('position'); // Change 'designation' to your desired name
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop the column if rolling back the migration
            $table->dropColumn('designation'); // Ensure this matches the name in the up method
        });
    }
}
