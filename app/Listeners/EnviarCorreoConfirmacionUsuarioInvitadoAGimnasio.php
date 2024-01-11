<?php

namespace App\Listeners;

use App\Events\UsuarioInvitadoAGimnasio;
use App\Notifications\CorreoConfirmacionUsuarioInvitadoAGimnasio;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class EnviarCorreoConfirmacionUsuarioInvitadoAGimnasio implements ShouldQueue
{
    public function handle(UsuarioInvitadoAGimnasio $event): void
    {
        Notification::send(
            $event->usuario,
            new CorreoConfirmacionUsuarioInvitadoAGimnasio(
                $event->usuario,
                $event->gimnasio,
                $event->token
            )
        );
    }
}
