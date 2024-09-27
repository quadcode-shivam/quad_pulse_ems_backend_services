<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';  // Specify the table name

    protected $fillable = [
        'user_id',
        'date',
        'status',
        'attendance_date',
        'check_in_time',
        'check_in_description',
        'check_out_time',
        'check_out_description',
    ];

    // Define the relationship with the Employee model
    public function employee()
    {
        // Link the 'user_id' in the Attendance model to the 'user_id' in the Employee model
        return $this->belongsTo(Employee::class, 'user_id', 'user_id');
    }
}
