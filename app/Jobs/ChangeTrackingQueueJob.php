<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Services\TrackingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChangeTrackingQueueJob implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    private $currentVersion;
    private $nextVersion;
    private $dados_CT;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($lastVersion, $nextVersion, $registrosERP_CT) {
        $this->queue = 'changeTracking';
        $this->currentVersion = $lastVersion;
        $this->nextVersion = $nextVersion;
        $this->dados_CT = $registrosERP_CT;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        //add/update na tabela "espelho"
        TrackingService::flushPediCliCad($this->dados_CT);

        //atualiza na tabela de configurações
        TrackingService::updateLastTrackingTable($this->nextVersion);
    }

}
