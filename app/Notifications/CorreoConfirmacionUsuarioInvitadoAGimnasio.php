<?php

namespace App\Notifications;

use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CorreoConfirmacionUsuarioInvitadoAGimnasio extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $usuario, public Gimnasio $gimnasio, public string $token){}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route("aceptar-invitacion", ["gimnasio" => $this->gimnasio->id, "hash" => $this->token]);

        return (new MailMessage)
            ->subject(__("emails.CorreoConfirmacionUsuarioInvitadoAGimnasio.asunto"))
            ->line(__("emails.CorreoConfirmacionUsuarioInvitadoAGimnasio.linea1", ["gimnasio" => $this->gimnasio->nombre]))
            ->line(__("emails.CorreoConfirmacionUsuarioInvitadoAGimnasio.linea2"))
            ->action(__("emails.CorreoConfirmacionUsuarioInvitadoAGimnasio.accion"), $url);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
