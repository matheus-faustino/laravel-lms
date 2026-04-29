<?php

namespace App\Enums;

enum EnrollmentStatusEnum: string
{
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
}
