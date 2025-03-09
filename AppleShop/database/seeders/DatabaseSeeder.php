<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Constants\Users\UserConstant;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            UserConstant::USER_ID => 1,
            UserConstant::USER_USERNAME => 'hht123',
            UserConstant::USER_PHONE_NUMBER => '0123456789',
            UserConstant::USER_DATE_OF_BIRTH => '1999-01-01',
            UserConstant::USER_IMG_AVATAR => null, 
            UserConstant::USER_PASSWORD => bcrypt('123456'),
            UserConstant::USER_EMAIL => 'hieukaijay@gmail.com',
            UserConstant::OTP => 'AAA999',
            UserConstant::OTP_EXPIRATION => Carbon::now(),
            UserConstant::USER_ROLES => 1,
            UserConstant::USER_IS_ACTIVED => 1,
            UserConstant::CREATE_AT => Carbon::now(),
        ]);
    }
}
