<?php

declare(strict_types = 1);

namespace App\Enums;

enum StatusEnum: int {
    case ENABLED = 1;
    case DISABLED = 0;
}
