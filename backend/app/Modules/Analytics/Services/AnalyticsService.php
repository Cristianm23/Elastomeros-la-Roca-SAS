<?php

namespace App\Modules\Analytics\Services;

use App\Modules\Analytics\Models\AnalyticsEvent;
use Illuminate\Http\Request;

class AnalyticsService
{
    public function record(array $data, Request $request): AnalyticsEvent
    {
        $properties = collect($data['properties'] ?? [])
            ->only(['query_length', 'result_count', 'source', 'status', 'error_type', 'target'])
            ->map(fn (mixed $value): string => substr((string) $value, 0, 160))
            ->all();

        return AnalyticsEvent::create([
            'user_id' => $request->user()?->id,
            'event_name' => $data['event_name'],
            'path' => isset($data['path']) ? substr((string) parse_url($data['path'], PHP_URL_PATH), 0, 2048) : null,
            'product_slug' => $data['product_slug'] ?? null,
            'properties' => $properties ?: null,
        ]);
    }
}
