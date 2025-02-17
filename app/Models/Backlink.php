<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backlink extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'website',
        'anchor_text',
        'status',
        'comments',
        'date',
        'completed',
        'checked', 
    ];
}
