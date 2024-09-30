<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'user_id',
        'name', 
        'email', 
        'password', 
        'country', 
        'state', 
        'address',
        'mobile', 
        'role', 
        'trash',
        'secret_key',
        'status',
        'position',        // Add position
        'designation',     // Add designation
        'salary',          // Add salary
        'date_hired',      // Add date_hired
        'created_at',      // Add created_at
        'updated_at',      // Add updated_at
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $primaryKey = 'id'; 

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id', 'user_id'); 
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
