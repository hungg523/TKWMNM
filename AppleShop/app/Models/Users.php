<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\Users\UserConstant;


class Users extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $fillable = [
        UserConstant::USER_ID,
        UserConstant::USER_USERNAME,
        UserConstant::USER_PHONE_NUMBER,
        UserConstant::USER_RENDER,
        UserConstant::USER_DATE_OF_BIRTH,
        UserConstant::USER_IMG_AVATAR,
        UserConstant::USER_PASSWORD,
        UserConstant::USER_EMAIL,
        UserConstant::OTP,
        UserConstant::OTP_EXPIRATION,
        UserConstant::USER_ROLES,
        UserConstant::USER_IS_ACTIVED,
        UserConstant::CREATE_AT,
    ];

    public $timestamps = false;
}
