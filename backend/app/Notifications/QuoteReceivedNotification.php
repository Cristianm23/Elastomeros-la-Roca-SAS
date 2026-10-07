<?php

namespace App\Notifications;

use App\Modules\Quotes\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Quote $quote, private readonly bool $forCompany = false) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->forCompany) {
            return (new MailMessage)
                ->subject('Nueva solicitud de cotización '.$this->quote->public_reference)
                ->greeting('Nueva solicitud recibida')
                ->line('Referencia: '.$this->quote->public_reference)
                ->line('Cliente: '.$this->quote->contact_name.' · '.$this->quote->company_name)
                ->line('Correo: '.$this->quote->email)
                ->line('Ciudad: '.($this->quote->city ?: 'No indicada'))
                ->line('Estado: '.$this->quote->status->value);
        }

        return (new MailMessage)
            ->subject('Recibimos su solicitud '.$this->quote->public_reference)
            ->greeting('Gracias por contactarnos, '.$this->quote->contact_name)
            ->line('Recibimos su solicitud de cotización y nuestro equipo la revisará pronto.')
            ->line('Referencia: '.$this->quote->public_reference)
            ->line('Estado: '.$this->quote->status->value)
            ->line('Conserve esta referencia para futuras consultas.');
    }

    public function afterCommit(): bool
    {
        return true;
    }
}
