<?php

namespace App\Modules\Quotes\Models;

enum QuoteStatus: string
{
    case PENDING = 'pending';
    case REVIEWING = 'reviewing';
    case QUOTED = 'quoted';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
