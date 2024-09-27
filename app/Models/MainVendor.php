<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainVendor extends Model
{
    use HasFactory;

    protected $table = 'mainvendor';

    protected $fillable = [
        'shop_id', // Added shop_id
        'shop_name',
        'shopkeeper_name',
        'location',
        'email',
        'authorization_key',
        'contact_number',
        'alternate_number',
        'description',
        'service_type',
        'opening_time',
        'closing_time',
        'sunday_off',
        'monday_off',
        'tuesday_off',
        'wednesday_off',
        'thursday_off',
        'friday_off',
        'saturday_off',
        'max_appointments_per_day',
        'appointment_duration_minutes',
        'requires_confirmation',
        'status',
        'trash',
    ];
}
