<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum GuestConfirmationStatus: string
{
    use BaseEnum;

    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case DECLINED = 'declined';

}
