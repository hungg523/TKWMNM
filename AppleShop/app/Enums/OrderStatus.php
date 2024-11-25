<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'Pending';
    case ACCEPTED = 'Accepted';
    case SHIPPING = 'Shipping';
    case SUCCEEDED = 'Successed';
    case CANCELED = 'Canceled';
}