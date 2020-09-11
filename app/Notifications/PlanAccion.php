<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PlanAccion extends Notification
{
    use Queueable;

    protected $planAccion;
    protected $segmento;
    protected $empresa;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($planAccion, $segmento, $empresa)
    {
        $this->planAccion = $planAccion;
        $this->segmento = $segmento;
        $this->empresa = $empresa;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Notificación - Planes de Acción')
                    ->markdown('emails.notificaciones.planaccion', [
                        'planAccion' => $this->planAccion
                        , 'segmento' => $this->segmento
                        , 'empresa' => $this->empresa
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
