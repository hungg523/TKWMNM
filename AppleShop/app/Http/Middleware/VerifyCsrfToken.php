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
        '/product/get-by-name/*',

        //coupon
        '/coupons/create',
        '/get-all',
        '/coupons/update/*',
        '/coupons/delete/*',
        '/coupons/get-by-id/*',

        //categories
        '/category/create',
        '/get-all',
        '/category/update/*',
        '/category/delete/*',
        '/category/get-by-id/*',
    ];
}
