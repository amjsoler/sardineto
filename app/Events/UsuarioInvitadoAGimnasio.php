<?php

namespace App\Events;

use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UsuarioInvitadoAGimnasio
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $usuario, public Gimnasio $gimnasio, public string $token)
    {
        Log::debug("Entrando en en dispatch del evento UsuarioInvitadoAGimnasio", compact(["usuario", "gimnasio", "token"]));
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
