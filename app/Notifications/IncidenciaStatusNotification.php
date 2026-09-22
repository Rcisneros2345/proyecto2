<?php

namespace App\Notifications;

use App\Models\Incidencia;
use Illuminate\Notifications\Notification;

class IncidenciaStatusNotification extends Notification
{
    public function __construct(
        public Incidencia $incidencia,
        public string $event,
        public string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toDatabase(object $notifiable): array
    {
        return [
            'event' => $this->event,
            'title' => 'Actualización de incidencia',
            'message' => $this->message,
            'incidencia_id' => $this->incidencia->id,
            'url' => route('incidencias.index'),
        ];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
