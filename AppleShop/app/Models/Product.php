<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\Product\ProductConstant;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = ProductConstant::PRODUCT_ID;

    protected $keyType = 'int';

    protected $fillable = [
        ProductConstant::PRODUCT_ID,
        ProductConstant::PRODUCT_NAME,
        ProductConstant::PRODUCT_DESCRIPTION,
        ProductConstant::PRODUCT_PRICE,
        ProductConstant::PRODUCT_DISCOUNT,
        ProductConstant::PRODUCT_QUANTITY,
        ProductConstant::IS_ACTIVED,
        ProductConstant::PRODUCT_COLOR,
        ProductConstant::CATEGORY_ID,
        ProductConstant::IMG_URL,
    ];
    public $timestamps = false;
}
