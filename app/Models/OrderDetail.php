<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'game_id',
        'quantity',
        'price',
    ];

    /**
     * Relasi ke Order (One-to-Many)
     * Setiap order detail terhubung ke satu order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke Game (One-to-Many)
     * Setiap order detail terhubung ke satu game
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
