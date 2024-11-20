<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //product
        '/products/create-product',
        '/get-products',
        '/products/update-product/*',
        '/products/delete-product/*',
        '/products/get-product-by-id/*',

        //coupon
        '/coupons/create-coupon',
        '/get-coupons',
        '/coupons/update-coupon/*',
        '/coupons/delete-coupon/*',
        '/coupons/get-coupon-by-id/*',
    ];
}
