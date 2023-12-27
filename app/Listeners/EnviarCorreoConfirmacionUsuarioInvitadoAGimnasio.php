<?php

namespace App\Listeners;

use App\Events\UsuarioInvitadoAGimnasio;
use App\Notifications\CorreoConfirmacionUsuarioInvitadoAGimnasio;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class EnviarCorreoConfirmacionUsuarioInvitadoAGimnasio implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        Log::debug("Entrando al listener EnviarCorreoConfirmacionUsuarioInvitadoAGimnasio");
    }

    /**
     * Handle the event.
     */
    public function handle(UsuarioInvitadoAGimnasio $event): void
    {
        Notification::send($event->usuario, new CorreoConfirmacionUsuarioInvitadoAGimnasio($event->usuario, $event->gimnasio, $event->token));
    }
}
