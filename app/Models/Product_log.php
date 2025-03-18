<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_log extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'name',
        'image',
        'rare',
        'point',
        'gacha_record_id',
        'user_id',
        'status',
    ];
}
