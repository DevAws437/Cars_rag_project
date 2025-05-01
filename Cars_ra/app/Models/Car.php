<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
class Car extends Model
{
    use HasFactory;
    use HasRoles;
    protected $fillable = [
        'model',
        'year',
        'color',
        'features',
        'seats',
        'status',
        'location',
        'daily_price',
        'image',
        'user_id',  // In order to connect to the user
    ];

    // Relationship with the user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discounts()
{
    return $this->hasMany(Discount::class);
}


    // Relationship with the reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }



}
