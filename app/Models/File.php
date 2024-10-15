<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ['item_id', 'file_path', 'extension'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
