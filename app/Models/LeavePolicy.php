<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeavePolicy extends Model
{
    // Define the table name if it's not 'holidays'
    protected $table = 'leave_policies';

    // Allow mass assignment for the columns
    protected $fillable = ['id', 'total_leave', 'total_half_day','total_late'];
}
