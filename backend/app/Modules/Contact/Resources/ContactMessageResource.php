<?php

namespace App\Modules\Contact\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'status' => $this->status, 'created_at' => $this->created_at];
    }
}
