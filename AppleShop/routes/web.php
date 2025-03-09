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
use App\Http\Controllers\Product\GetAllsProductController;
use App\Http\Controllers\Product\GetProductDeatilController;
use App\Http\Controllers\Product\GetProductByCategoryIdController;
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
use App\Http\Controllers\Users\GetByEmailUserController;
use App\Http\Controllers\Users\UpdateUserProfileController;


//UserAddress
use App\Http\Controllers\UsersAddress\CreateUserAddressController;
use App\Http\Controllers\UsersAddress\UpdateUserAddressController;
use App\Http\Controllers\UsersAddress\GetAllUserAddressController;
use App\Http\Controllers\UsersAddress\GetByIdUserAddressController;
use App\Http\Controllers\UsersAddress\GetCustomerAddressByCustomerId;
// Order
use App\Http\Controllers\Order\CreateOrderController;
use App\Http\Controllers\Order\ChangeStatusController;
use App\Http\Controllers\Order\GetAllOrderController;
use App\Http\Controllers\Order\GetOrderByCustomerIdController;
use App\Http\Controllers\Order\GetOrderByIdController;
//Order-Items
use App\Http\Controllers\Order\GetOrderItemByOrderId;



Route::prefix('product')->group(function () {
    Route::get('/get-all', [GetAllProductController::class, 'index']);
    Route::post('/create', [CreateProductController::class, 'store']);
    Route::put('/update/{id}', [UpdateProductController::class, 'update']);
    Route::get('/get-by-name', [GetByNameProductController::class, 'getbyname']);
    Route::get('/get-by-id/{id}', [GetByIdProductController::class, 'show']);
    Route::get('/get-alls', [GetAllsProductController::class, 'getAllsProducts']);
    Route::get('/get-detail/{id}', [GetProductDeatilController::class, 'getProductDetail']);
    Route::get('/get-product-by-category/{id}', [GetProductByCategoryIdController::class, 'getProductsByCategoryId']);
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
    Route::get('/get-by-email', [GetByEmailUserController::class, 'getbyemail']);
    Route::put('/update-profile/{id}', [UpdateUserProfileController::class, 'updateprofile']);
});

Route::prefix('useraddress')->group(function () {
    Route::post('/create', [CreateUserAddressController::class, 'store']);
    //Route::post('/register', [RegissterUserController::class, 'register']);
    Route::put('/update/{id}', [UpdateUserAddressController::class, 'update']);
    Route::get('/get-all', [GetAllUserAddressController::class, 'index']);
    Route::get('/get-address-by-customer-id/{id}', [GetCustomerAddressByCustomerId::class, 'getcustomer']);
    Route::get('/get-by-id/{id}', [GetByIdUserAddressController::class, 'show']);
});

Route::prefix('order')->group(function () {
    Route::post('/create', [CreateOrderController::class, 'create']);
    Route::put('/change-status/{id}', [ChangeStatusController::class, 'changestatus']);
    Route::get('/get-all', [GetAllOrderController::class, 'index']);
    Route::get('/get-by-customer-id/{id}', [GetOrderByCustomerIdController::class, 'getordersbycustomerid']);
    Route::get('/get-by-id/{id}', [GetOrderByIdController::class, 'getorderbyid']);
});

Route::prefix('order-item')->group(function () {
    Route::get('/get-by-order-id/{id}', [GetOrderItemByOrderId::class, 'getorderitemsbyorderid']);
});