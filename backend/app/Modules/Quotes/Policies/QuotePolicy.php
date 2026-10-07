<?php

namespace App\Modules\Quotes\Policies;

use App\Models\User;
use App\Modules\Quotes\Models\Quote;

class QuotePolicy
{
    public function view(User $user, Quote $quote): bool
    {
        return $quote->user_id === $user->id || $user->hasPermission('quotes.view');
    }
}
