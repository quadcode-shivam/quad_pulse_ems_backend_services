<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'description',   
        'half_day_full_day',  
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
