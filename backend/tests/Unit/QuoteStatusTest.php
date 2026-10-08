<?php

namespace Tests\Unit;

use App\Modules\Quotes\Models\QuoteStatus;
use PHPUnit\Framework\TestCase;

class QuoteStatusTest extends TestCase
{
    public function test_quote_statuses_match_public_workflow(): void
    {
        self::assertSame(
            ['pending', 'reviewing', 'quoted', 'accepted', 'rejected', 'completed', 'cancelled'],
            array_column(QuoteStatus::cases(), 'value'),
        );
    }
}
