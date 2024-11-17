<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImg extends Model
{
    use HasFactory;

    protected $table = 'productimg';

    protected $fillable = [
        'ProductId',
        'ImgUrl',
        'ImgName',
        'ImgOrder',
    ];
}
