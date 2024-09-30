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
        return $this->belongsTo(Employee::class, 'user_id', 'user_id'); // Check foreign key mapping
    }

    // Optional: if you need the relationship to the User
    public function user()
    {
        return $this->hasOneThrough(User::class, Employee::class, 'user_id', 'user_id', 'user_id', 'user_id');
    }
}
