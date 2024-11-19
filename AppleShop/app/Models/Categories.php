<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// class Category extends Model
// {
//     use HasFactory;

//     // Đặt tên bảng trong cơ sở dữ liệu
//     protected $table = 'categories'; 

//     // Khai báo các cột có thể được gán giá trị hàng loạt (mass assignable)
//     protected $fillable = [
//         'CategoryId',    // ID của danh mục
//         'CategoryName',  // Tên danh mục
//     ];

//     // Định nghĩa quan hệ với bảng products
//     public function products()
//     {
//         return $this->hasMany(Product::class, 'CategoryId', 'CategoryId');
//     }
// }
