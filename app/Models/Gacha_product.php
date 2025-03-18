<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gacha_product extends Model
{
    use HasFactory;
    protected $fillable = [
        'gacha_id',
        'product_id',
        'is_last',
        'order',
        'count',
    ];
}
