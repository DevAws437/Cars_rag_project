<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'car_id',
        'rating',
        'comment',
    ];

    // Relationship with the user
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
