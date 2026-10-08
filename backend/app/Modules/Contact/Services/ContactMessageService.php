<?php

namespace App\Modules\Contact\Services;

use App\Modules\Contact\Models\ContactMessage;
use App\Notifications\ContactReceivedNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Modules\Analytics\Models\AnalyticsEvent;

class ContactMessageService
{
    public function create(array $data): ContactMessage
    {
        $message = ContactMessage::create([
            ...$data,
            'consent_at' => Carbon::now(),
            'status' => 'unread',
        ]);
        AnalyticsEvent::create(['event_name' => 'contact_submitted']);

        Notification::route('mail', $message->email)->notify(new ContactReceivedNotification($message));
        Notification::route('mail', config('mail.sales_address'))->notify(new ContactReceivedNotification($message, true));

        return $message;
    }
}
