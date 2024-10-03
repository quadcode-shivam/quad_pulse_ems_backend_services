<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    // Define the table name if it's not 'holidays'
    protected $table = 'ind_holiday';

    // Allow mass assignment for the columns
    protected $fillable = ['holiday_name', 'holiday_desc', 'holiday_date'];
}
