<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CheckIn extends Model
{
    use HasFactory;

    protected $table = 'checkins'; // Specify the correct table name

    protected $fillable = [
        'employee_id',
        'status',
        'user_name',
        'email',
        'role',
        'check_in_time',
        'check_out_time',
        'check_in_info',
        'check_out_info',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }
}
