<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\UsersAddress\UsersAddressConstant;


class UserAddress extends Model
{
    use HasFactory;

    protected $table = 'user_address';
    protected $primaryKey = UsersAddressConstant::USER_ADDRESS_ID;

    protected $keyType = 'int';

    protected $fillable = [
        UsersAddressConstant::USER_ADDRESS_ID,
        UsersAddressConstant::USER_ID,
        UsersAddressConstant::WARD,
        UsersAddressConstant::DISTRICT,
        UsersAddressConstant::PROVINCE,
        UsersAddressConstant::TEL,
        UsersAddressConstant::IS_ACTIVED,
        UsersAddressConstant::FULL_NAME,
        UsersAddressConstant::ADDRESS
    ];
    public $timestamps = false;
}
