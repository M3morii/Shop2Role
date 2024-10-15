<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['item_id', 'buyprice', 'finalstock'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }
}
