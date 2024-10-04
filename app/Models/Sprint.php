<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    protected $fillable = [
        'name',
        'goal',
        'start_date',
        'end_date',
    ];
}
