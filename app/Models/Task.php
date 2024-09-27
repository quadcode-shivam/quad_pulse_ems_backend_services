<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'employee_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }
}
