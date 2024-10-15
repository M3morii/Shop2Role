<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['name', 'description', 'sellprice'];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
