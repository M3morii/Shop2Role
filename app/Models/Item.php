<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Item extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'sellprice', 'stock', 'category_id'];

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
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
