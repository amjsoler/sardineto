<?php

namespace App\Notifications;

use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CorreoConfirmacionUsuarioInvitadoAGimnasio extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $usuario, public Gimnasio $gimnasio, public string $token)
    {
        Log::debug("Entrando a CorreoConfirmacionUsuarioInvitadoAGimnasio", compact(["usuario", "gimnasio", "token"]));
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route("aceptar-invitacion", ["gimnasio" => $this->gimnasio->id, "hash" => $this->token]);

        return (new MailMessage)
            ->subject(__("emails.CorreoConfirmacionUsuarioInvitadoAGimnasio.asunto"))
            ->line(__("emails.CorreoConfirmacionUsuarioInvitadoAGimnasio.linea1", ["gimnasio" => $this->gimnasio->nombre]))
            ->line(__("emails.CorreoConfirmacionUsuarioInvitadoAGimnasio.linea2"))
            ->action(__("emails.CorreoConfirmacionUsuarioInvitadoAGimnasio.accion"), $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
