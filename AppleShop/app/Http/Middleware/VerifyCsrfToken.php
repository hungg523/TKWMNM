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
        '/product/create',
        '/get-all',
        '/product/update/*',
        '/product/delete/*',
        '/product/get-by-id/*',
        '/product/get-by-name',

        //coupon
        '/coupon/create',
        '/get-all',
        '/coupon/update/*',
        '/coupon/delete/*',
        '/coupon/get-by-id/*',

        //categories
        '/category/create',
        '/get-all',
        '/category/update/*',
        '/category/delete/*',
        '/category/get-by-id/*',

        //Users
        '/user/register',
        '/user/vertify-otp',
        '/user/resend-otp',
        '/user/login',

        //Order
        '/order/create',
    ];
}
