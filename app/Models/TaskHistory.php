<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    protected $table = 'task_history'; // Specify the table name if it's not the default

    protected $fillable = [
        'task_id',       
        'employee_id',   
        'action',       
        'previous_data', 
        'new_data',    
    ];

    // Define relationships if necessary
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
