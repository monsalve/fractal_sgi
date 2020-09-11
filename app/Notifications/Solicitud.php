<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Solicitud extends Notification
{
    use Queueable;

    protected $observacion;
    protected $autor;
    protected $cargo;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($observacion, $autor, $cargo)
    {
        $this->observacion = $observacion;
        $this->autor = $autor;
        $this->cargo = $cargo;
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
                    ->line('Notificación - Solicitudes')
                    ->markdown('emails.notificaciones.solicitud', [
                        'observacion' => $this->observacion
                        , 'autor' => $this->autor
                        , 'cargo' => $this->cargo
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
