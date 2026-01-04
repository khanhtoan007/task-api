<?php

namespace App\Http\Enums;

enum UserStatusEnum: string
{
    case ACTIVE = 'active';
    case BLOCKED = 'blocked';
    case PENDING = 'pending';
}
