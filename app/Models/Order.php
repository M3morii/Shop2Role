<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    protected $fillable = ['invoice_id', 'user_id', 'item_id', 'quantity', 'price', 'status'];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id'); // 'item_id' sesuai dengan nama kolom di tabel order
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
