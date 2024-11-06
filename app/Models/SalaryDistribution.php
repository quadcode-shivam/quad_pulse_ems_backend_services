<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDistribution extends Model
{
    use HasFactory;

    // Specify the table associated with the model (optional if the table name follows Laravel's naming conventions)
    protected $table = 'salary_distributions'; 

    // Specify the fillable attributes
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'salary_month',
        'transaction_date',
        'transaction_id',
        'payment_method',
        'currency',
        'notes',
    ];

    // Optionally, if you want to automatically manage timestamps
    public $timestamps = true; // This is true by default

    // You can also specify date format if needed
    protected $dates = [
        'transaction_date',
    ];
}
