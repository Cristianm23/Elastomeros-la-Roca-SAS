<?php

namespace App\Notifications;

use App\Modules\Quotes\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Quote $quote) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Actualización de su cotización '.$this->quote->public_reference)
            ->greeting('Actualizamos su solicitud')
            ->line('Su cotización '.$this->quote->public_reference.' ahora está en estado: '.$this->quote->status->value.'.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'quote_status_changed',
            'reference' => $this->quote->public_reference,
            'status' => $this->quote->status->value,
        ];
    }

    public function afterCommit(): bool
    {
        return true;
    }
}
