<?php

namespace App\Jobs;

use App\Actividad;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Actividad as ActividadNotificacion;

class ProcessActividad implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $actividad;
    protected $responsables;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Actividad $actividad, $responsables)
    {
        $this->actividad = $actividad;
        $this->responsables = $responsables;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Notification::send(
            !is_null($this->responsables)?:$actividad->responsables()->with('persona:id,email')->get()->pluck('persona')
            , new ActividadNotificacion($actividad)
        );
    }
}
