<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel orders.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();  // Kolom ID (auto increment)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');  // Foreign key ke customers
            $table->decimal('total_price', 10, 2);  // Kolom untuk harga total
            $table->enum('status', ['pending', 'completed', 'canceled'])->default('pending');  // Status pemesanan
            $table->timestamps();  // Kolom created_at dan updated_at
            $table->softDeletes(); 
        });
    }

    /**
     * Pembatalan migrasi (jika dibutuhkan untuk rollback).
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
