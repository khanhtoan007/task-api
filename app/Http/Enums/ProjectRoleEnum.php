<?php

namespace App\Http\Enums;

enum ProjectRoleEnum: string
{
    case OWNER = 'owner';
    case MEMBER = 'member';

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::OWNER => in_array($to, [self::MEMBER]),
            self::MEMBER => in_array($to, [self::OWNER]),
        };
    }
}
