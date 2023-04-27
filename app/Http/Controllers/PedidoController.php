<?php

namespace App\Http\Controllers;

use App\Services\PedidoService;

class PedidoController extends Controller {

    public function index() {
        try {
            //Busco na tabela de configurações a ultima versão que utilizamos
            $lastVersion = PedidoService::getLastVersionControle();

            //Busco a última versão do change tracking do SQL Server
            $updateVersion = PedidoService::getLastVersionTrackingTable();

            //busco as ultimas alteraçẽos no ERP
            $pedidosTrackingERP = PedidoService::getLastChagingTrackingERP($lastVersion);

            $chunks = array_chunk($pedidosTrackingERP, 500);
            foreach ($chunks as $chunk) {
                //add/update na tabela "espelho"
                PedidoService::flushPediCliCad($chunk);
            }

            //atualiza na tabela de configurações
            PedidoService::updateLastTrackingTable($updateVersion);

            dump('Last Execution: ' . (new \DateTime())->format('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
