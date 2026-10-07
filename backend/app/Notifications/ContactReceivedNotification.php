<?php

namespace App\Notifications;

use App\Modules\Contact\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly ContactMessage $message, private readonly bool $forCompany = false) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->forCompany) {
            return (new MailMessage)
                ->subject('Nuevo mensaje de contacto: '.$this->message->subject)
                ->greeting('Nuevo mensaje recibido')
                ->line('De: '.$this->message->name.' · '.($this->message->company_name ?: 'Sin empresa'))
                ->line('Correo: '.$this->message->email)
                ->line('Asunto: '.$this->message->subject)
                ->line($this->message->message);
        }

        return (new MailMessage)
            ->subject('Recibimos su mensaje')
            ->greeting('Gracias por escribirnos, '.$this->message->name)
            ->line('Recibimos su mensaje y nuestro equipo se pondrá en contacto pronto.')
            ->line('Asunto: '.$this->message->subject);
    }

    public function afterCommit(): bool
    {
        return true;
    }
}
