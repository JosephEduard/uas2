<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Game extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'platform',
        'genre',
        'price',
        'stock',
        'description',
        'thumbnail',
    ];

    /**
     * Relasi ke GameImage (one-to-many).
     * Satu game dapat memiliki banyak gambar.
     */
    public function images()
    {
        return $this->hasMany(GameImage::class);
    }

    protected static function booted()
    {
        static::creating(function ($game) {
            $game->slug = Str::slug($game->title);
        });

        static::updating(function ($game) {
            if ($game->isDirty('title')) {
                $game->slug = Str::slug($game->title);
            }
        });
    }

    // Accessor untuk format harga
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 2, ',', '.');
    }

    // Accessor untuk ketersediaan stok
    public function getIsAvailableAttribute()
    {
        return $this->stock > 0 ? 'Available' : 'Out of Stock';
    }

    // Accessor untuk mendapatkan URL thumbnail
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }
}
