<?php

namespace App\Jobs;

use App\PlanAccion;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\PlanAccion as PlanAccionNotificacion;

class ProcessPlanAccion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $planAccion;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PlanAccion $planAccion)
    {
        $this->planAccion = $planAccion;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->planAccion->responsable->persona->notify(new PlanAccionNotificacion($this->planAccion));
    }
}
