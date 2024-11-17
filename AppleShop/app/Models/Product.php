<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'ProductId',
        'ProductName',
        'Description',
        'Price',
        'Discount',
        'Quantity',
        'IsActived',
        'Created',
        'Updated',
        'ProductDetail',
    ];

    protected $casts = [
        'IsActived' => 'boolean',
        'Created' => 'datetime',
        'Updated' => 'datetime',
    ];
}
