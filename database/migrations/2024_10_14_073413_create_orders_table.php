<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relasi ke tabel users
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // Relasi ke tabel items
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade'); // Relasi ke tabel items
            $table->integer('quantity');
            $table->string('status')->default('pending');
            $table->decimal('price', 10, 2); // Harga saat pemesanan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
