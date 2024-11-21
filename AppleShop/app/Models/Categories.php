<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\Categories\CategoriesConstant;

class Categories extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $primaryKey = CategoriesConstant::CATEGORY_ID;

    protected $keyType = 'int';

    protected $fillable = [
        CategoriesConstant::CATEGORY_ID,
        CategoriesConstant::CATEGORY_NAME,
    ];

    public $timestamps = false;
}
