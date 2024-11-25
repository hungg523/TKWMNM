<?php
use Illuminate\Support\Facades\Route;

//category
use App\Http\Controllers\Category\CreateCategoriesController;
use App\Http\Controllers\Category\UpdateCategoriesController;
use App\Http\Controllers\Category\GetAllCategoriesController;
use App\Http\Controllers\Category\GetByIdCategoriesController;
//product
use App\Http\Controllers\Product\CreateProductController;
use App\Http\Controllers\Product\UpdateProductController;
use App\Http\Controllers\Product\GetAllProductController;
use App\Http\Controllers\Product\GetByIdProductController;
use App\Http\Controllers\Product\GetByNameProductController;
//Coupon
use App\Http\Controllers\Coupon\CreateCouponController;
use App\Http\Controllers\Coupon\UpdateCouponController;
use App\Http\Controllers\Coupon\GetAllCouponController;
use App\Http\Controllers\Coupon\GetByIdCouponController;
//User
use App\Http\Controllers\Users\RegissterUserController;
use App\Http\Controllers\Users\AuthenUserController;
use App\Http\Controllers\Users\ResentOtpUserController;
use App\Http\Controllers\Users\LoginUserController;
use App\Http\Controllers\Users\GetAllUserController;
use App\Http\Controllers\Users\GetByIdUserController;
use App\Http\Controllers\Users\ChangePasswordController;
use App\Http\Controllers\Users\UpdateUserPasswordController;
//UserAddress
use App\Http\Controllers\UsersAddress\CreateUserAddressController;
use App\Http\Controllers\UsersAddress\UpdateUserAddressController;
use App\Http\Controllers\UsersAddress\GetAllUserAddressController;
use App\Http\Controllers\UsersAddress\GetByIdUserAddressController;
use App\Http\Controllers\UsersAddress\GetCustomerAddressByCustomerId;



Route::prefix('product')->group(function () {
    Route::get('/get-all', [GetAllProductController::class, 'index']);
    Route::post('/create', [CreateProductController::class, 'store']);
    Route::put('/update/{id}', [UpdateProductController::class, 'update']);
    Route::get('/get-by-name', [GetByNameProductController::class, 'getbyname']);
    Route::get('/get-by-id/{id}', [GetByIdProductController::class, 'show']);
});

Route::prefix('coupon')->group(function () {
    Route::get('/get-all', [GetAllCouponController::class, 'index']);
    Route::post('/create', [CreateCouponController::class, 'store']);
    Route::put('/update/{id}', [UpdateCouponController::class, 'update']);
    //Route::delete('/delete/{id}', [CouponController::class, 'destroy']);
    Route::get('/get-by-id/{id}', [GetByIdCouponController::class, 'show']);
});

Route::prefix('category')->group(function () {
    Route::get('/get-all', [GetAllCategoriesController::class, 'index']);
    Route::post('/create', [CreateCategoriesController::class, 'store']);
    Route::put('/update/{id}', [UpdateCategoriesController::class, 'update']);
    Route::get('/get-by-id/{id}', [GetByIdCategoriesController::class, 'show']);
});

Route::prefix('user')->group(function () {
    Route::post('/resend-otp', [ResentOtpUserController::class, 'resendOtp']);
    Route::post('/register', [RegissterUserController::class, 'register']);
    Route::put('/vertify-otp', [AuthenUserController::class, 'authen']);
    Route::get('/get-all', [GetAllUserController::class, 'index']);
    Route::post('/login', [LoginUserController::class, 'login']);
    Route::get('/get-by-id/{id}', [GetByIdUserController::class, 'show']);
    Route::post('/change-password', [ChangePasswordController::class, 'changePassword']);
    Route::put('/update-password', [UpdateUserPasswordController::class, 'updatePassword']);

});

Route::prefix('useraddress')->group(function () {
    Route::post('/create', [CreateUserAddressController::class, 'store']);
    //Route::post('/register', [RegissterUserController::class, 'register']);
    Route::put('/update/{id}', [UpdateUserAddressController::class, 'update']);
    Route::get('/get-all', [GetAllUserAddressController::class, 'index']);
    Route::get('/get-address-by-customer-id/{id}', [GetCustomerAddressByCustomerId::class, 'getcustomer']);
    Route::get('/get-by-id/{id}', [GetByIdUserAddressController::class, 'show']);
});