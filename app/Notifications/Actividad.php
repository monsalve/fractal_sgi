<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Actividad extends Notification
{
    use Queueable;

    protected $actividad;
    protected $planAccion;
    protected $segmento;
    protected $proyecto;
    protected $empresa;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($actividad, $planAccion, $segmento, $proyecto, $empresa)
    {
        $this->actividad = $actividad;
        $this->planAccion = $planAccion;
        $this->segmento = $segmento;
        $this->proyecto = $proyecto;
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
                    ->subject('Notificación - Actividad')
                    ->markdown('emails.notificaciones.actividad', [
                        'actividad' => $this->actividad
                        , 'planAccion' => $this->planAccion
                        , 'segmento' => $this->segmento
                        , 'proyecto' => $this->proyecto
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
