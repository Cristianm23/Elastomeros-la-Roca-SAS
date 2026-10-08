<?php

namespace App\Modules\Quotes\Services;

use App\Modules\Products\Models\Product;
use App\Modules\Quotes\Models\Quote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Notifications\QuoteReceivedNotification;
use App\Modules\Analytics\Models\AnalyticsEvent;

class QuoteService
{
    public function create(array $data, ?int $userId, ?string $sourceIp): Quote
    {
        return DB::transaction(function () use ($data, $userId, $sourceIp): Quote {
            $quote = Quote::create([
                'public_reference' => 'COT-'.strtoupper(Str::random(10)),
                'user_id' => $userId,
                'company_name' => $data['company_name'],
                'contact_name' => $data['contact_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'city' => $data['city'],
                'required_date' => $data['required_date'],
                'observations' => $data['observations'] ?? null,
                'attachment_path' => isset($data['attachment']) ? $data['attachment']->store('quotes', 'local') : null,
                'message' => $data['message'] ?? null,
                'status' => 'pending',
                'source_ip' => $sourceIp,
            ]);

            $items = collect($data['items'])->map(function (array $item): array {
                if (empty($item['description']) && ! empty($item['product_id'])) {
                    $item['description'] = Product::query()->whereKey($item['product_id'])->value('name');
                }

                return $item;
            })->all();

            $quote->items()->createMany($items);
            $quote = $quote->load('items');
            AnalyticsEvent::create([
                'user_id' => $userId,
                'event_name' => 'quote_submitted',
                'product_slug' => $data['product_slug'] ?? null,
            ]);
            Notification::route('mail', $quote->email)->notify(new QuoteReceivedNotification($quote));
            Notification::route('mail', config('mail.sales_address'))->notify(new QuoteReceivedNotification($quote, true));

            return $quote;
        });
    }
}
