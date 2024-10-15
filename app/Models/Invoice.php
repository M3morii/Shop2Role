<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['total_price', 'purchase_date'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
