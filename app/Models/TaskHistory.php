<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    protected $table = 'task_history'; // Specify the table name if it's not the default

    protected $fillable = [
        'task_id',        // The ID of the task associated with this history entry
        'employee_id',    // The ID of the employee who made the change
        'action',         // Description of the action taken (e.g., created, updated, moved)
        'previous_data',  // JSON or serialized data before the change
        'new_data',    
    ];

    // Define relationships if necessary
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
