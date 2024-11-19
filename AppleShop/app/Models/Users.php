<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $fillable = [
        'UsersId',
        'Username',
        'PhoneNumber',
        'Render',
        'DateOfBirth',
        'ImgAvatar',
        'Password',
        'Email',
        'Roles',
        'IsActived',
    ];

    protected $casts = [
        'DateOfBirth' => 'datetime',
        'IsActived' => 'boolean',
    ];
}
