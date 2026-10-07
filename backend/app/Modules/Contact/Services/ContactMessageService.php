<?php

namespace App\Modules\Contact\Services;

use App\Modules\Contact\Models\ContactMessage;
use Illuminate\Support\Carbon;

class ContactMessageService
{
    public function create(array $data): ContactMessage
    {
        return ContactMessage::create([
            ...$data,
            'consent_at' => Carbon::now(),
            'status' => 'unread',
        ]);
    }
}
