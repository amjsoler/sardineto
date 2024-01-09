<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecuperarCuenta extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token){}


    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route("recuperarcuentaget", $this->token);

        return (new MailMessage)
            ->subject(__("emails.recuperarcuenta.asunto"))
            ->line(__("emails.recuperarcuenta.linea1"))
            ->line(__("emails.recuperarcuenta.linea2"))
            ->action(__("emails.recuperarcuenta.accion"), $url);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
