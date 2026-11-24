<?php

namespace App\Enums;

enum RequestStatus: string
{
    case OPEN = 'open';
    case ANSWERED = 'answered';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Ouverte',
            self::ANSWERED => 'Répondue',
            self::CLOSED => 'Clôturée',
        };
    }
}
