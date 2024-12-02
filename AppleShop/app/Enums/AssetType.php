<?php

namespace App\Enums;

enum AssetType: int
{
    case PRODUCT_IMG = 0;
    case CAT_IMG = 1;
    case CUSTOMER = 2;
    case USER_IMG = 3;
    // Thêm các loại khác nếu cần
}
