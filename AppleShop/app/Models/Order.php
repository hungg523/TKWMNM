<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\Order\OrderConstant;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = OrderConstant::ORDER_ID;

    protected $keyType = 'int';

    protected $fillable = [
        OrderConstant::ORDER_ID,
        OrderConstant::USER_ID,
        OrderConstant::USER_ADDRESS_ID,
        OrderConstant::COUPON_ID,
        OrderConstant::ORDER_DATE,
        OrderConstant::SHIPPING_COST,
        OrderConstant::PAYMENT,
        OrderConstant::TOTAL_AMOUNT,
        OrderConstant::STATUS,
        OrderConstant::IS_ACTIVED
    ];

    public $timestamps = false;

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
