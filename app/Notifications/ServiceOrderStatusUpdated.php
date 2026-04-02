<?php

namespace App\Notifications;

use App\Models\ServiceOrder;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceOrderStatusUpdated extends Notification
{
    public function __construct(
        public ServiceOrder $serviceOrder
    ) {
        $this->serviceOrder->loadMissing(['client', 'vehicle', 'status']);
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->serviceOrder;
        $statusName = $order->status?->name ?? 'actualizado';
        $folio = $order->folio_number;
        $vehicle = $order->vehicle;

        $mail = (new MailMessage)
            ->subject('Actualización de su orden de servicio '.$folio)
            ->greeting('Hola '.$order->client?->name.',')
            ->line('Le informamos que el estado de su orden de servicio ha cambiado.')
            ->line('Folio: '.$folio)
            ->line('Estado actual: '.$statusName);

        if ($vehicle) {
            $mail->line('Vehículo: '.trim($vehicle->brand.' '.$vehicle->model).' · Placas: '.$vehicle->plate);
        }

        return $mail
            ->line('Puede consultar el avance en cualquier momento desde el sitio web del taller o el asistente virtual.')
            ->salutation('Saludos cordiales — Taller ISTAE');
    }
}
