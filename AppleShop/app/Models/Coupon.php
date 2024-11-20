<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\Coupon\CouponConstant;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupon';
    protected $primaryKey = CouponConstant::COUPON_ID;

    protected $keyType = 'int';

    protected $fillable = [
        CouponConstant::COUPON_ID,
        CouponConstant::COUPON_CODE,
        CouponConstant::COUPON_DESCRIPTION,
        CouponConstant::COUPON_START_DATE,
        CouponConstant::COUPON_END_DATE,
    ];

    public $timestamps = false;
}
