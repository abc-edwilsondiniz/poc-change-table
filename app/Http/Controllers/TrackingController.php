<?php

namespace App\Http\Controllers;

use App\Jobs\ChangeTrackingQueueJob;
use App\Services\TrackingService;

class TrackingController extends Controller {

    public function index() {
        try {
            //Busco na tabela de configurações a ultima versão que utilizamos
            $lastVersion = TrackingService::getLastVersionControle();

            //Busco a última versão do change tracking do SQL Server
            $updateVersion = TrackingService::getLastVersionTrackingTable();

            //busco as ultimas alteraçẽos no ERP
            $dadosTrackingERP = TrackingService::getLastChagingTrackingERP($lastVersion);

            ChangeTrackingQueueJob::dispatch($lastVersion, $updateVersion, $dadosTrackingERP);

            dump('Last Execution: ' . (new \DateTime())->format('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
