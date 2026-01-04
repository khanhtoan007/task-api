<?php

namespace App\Http\Enums;

enum ProjectRoleEnum: string
{
    case OWNER = 'owner';
    case CONTRIBUTOR = 'contributor';

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::OWNER => $to === self::CONTRIBUTOR,
            self::CONTRIBUTOR => $to === self::OWNER,
        };
    }
}
