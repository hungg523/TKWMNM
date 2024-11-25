<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $table = 'useraddress';

    protected $fillable = [
        'UserAddressesId',
        'Ward',
        'District',
        'Province',
        'Tel',
        'IsActived',
    ];

    protected $casts = [
        'IsActived' => 'boolean',
    ];
}
