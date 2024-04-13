<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskStatus: String
{
    case OPEN = 'open';
    case CLOSED = 'closed';
    case DURING = 'during';
}
