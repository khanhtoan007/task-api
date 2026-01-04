<?php

namespace App\Http\Enums;

enum UserTokenTypeEnum: string
{
    case VERIFY_EMAIL = 'verify_email';
    case RESET_PASSWORD = 'reset_password';
    case INVITE_USER = 'invite_user';
    case MAGIC_LOGIN = 'magic_login';
}
