<?php

namespace App\Events;

use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UsuarioInvitadoAGimnasio
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public User $usuario, public Gimnasio $gimnasio, public string $token){}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
