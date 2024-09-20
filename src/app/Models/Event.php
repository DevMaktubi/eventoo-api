<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'event_date',
        'available_seats',
        'thumbnail_url'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'available_seats' => 'integer',
    ];
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
