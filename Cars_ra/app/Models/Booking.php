<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'car_id',
        'start_date',
        'end_date',
        'location',
        'status',
    ];

    // For user relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with the car
    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
