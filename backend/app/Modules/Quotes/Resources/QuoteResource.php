<?php

namespace App\Modules\Quotes\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'reference' => $this->public_reference,
            'status' => $this->status,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'description' => $item->description,
                'quantity' => $item->quantity,
                'notes' => $item->notes,
            ])->values()),
            'created_at' => $this->created_at,
        ];
    }
}
