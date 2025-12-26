<?php

namespace App\Http\Enums;

enum ProjectStatusEnum: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case ON_HOLD = 'on_hold';
    case COMPLETED = 'completed';
    case ARCHIVED = 'archived';
    case CANCELLED = 'cancelled';

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::DRAFT, self::ON_HOLD => in_array($to, [self::ACTIVE, self::CANCELLED]),
            self::ACTIVE => in_array($to, [self::ON_HOLD, self::COMPLETED, self::CANCELLED]),
            self::COMPLETED => [self::ARCHIVED],
            self::ARCHIVED, self::CANCELLED => [],
        };
    }
}