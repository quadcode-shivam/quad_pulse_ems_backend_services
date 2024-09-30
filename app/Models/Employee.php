<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id', 
        'position', 
        'designation', 
        'date_hired', 
        'status', 
        'created_at', 
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id'); // Specify the foreign key correctly
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'user_id', 'user_id'); // Ensure the foreign key matches
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}

