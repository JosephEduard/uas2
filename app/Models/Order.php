<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;  // Menambahkan HasFactory

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'user_id',
        'total_price',
        'status',
    ];

    // Menentukan kolom 'deleted_at' sebagai kolom tanggal
    protected $dates = ['deleted_at'];

    /**
     * Relasi ke Customer (many-to-one).
     * Setiap order dimiliki oleh satu customer.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke OrderItem (one-to-many).
     * Satu order dapat memiliki banyak item (game yang dipesan).
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * Accessor untuk format harga total
     */
    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 2, ',', '.');
    }

    /**
     * Menyaring pesanan berdasarkan status
     * - Misalnya untuk mencari semua pesanan dengan status 'pending'
     */
    public static function getOrdersByStatus($status)
    {
        return self::where('status', $status)->get();
    }
}
