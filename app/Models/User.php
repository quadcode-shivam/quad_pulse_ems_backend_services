<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'country', 
        'state', 
        'address',
        'mobile', 
        'role', 
        'trash',
        'user_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
