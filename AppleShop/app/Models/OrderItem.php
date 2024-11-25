<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\OrderItem\OrderItemConstant;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_item';
    protected $primaryKey = OrderItemConstant::ORDER_ITEM_ID;

    protected $keyType = 'int';

    protected $fillable = [
        OrderItemConstant::PRODUCT_ID ,
        OrderItemConstant::ORDER_ID ,
        OrderItemConstant::ORDER_ITEM_ID,
        OrderItemConstant::QUANTITY,
        OrderItemConstant::UNIT_PRICE,
        OrderItemConstant::TOTAL
    ];

    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
