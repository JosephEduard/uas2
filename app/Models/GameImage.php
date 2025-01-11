<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GameImage extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'game_id',
        'image_path',
    ];

    /**
     * Relasi ke Game (many-to-one).
     * Satu gambar hanya dimiliki oleh satu game.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
