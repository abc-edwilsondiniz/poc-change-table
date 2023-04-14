<?php

namespace App\Http\Controllers;

use App\Services\EstoqueService;

class EstoqueController extends Controller {

    public function estoque() {
        try {
            //Busco na tabela de configurações a ultima versão que utilizamos
            $lastVersion = EstoqueService::getLastVersionControle();

            //Busco a última versão do change tracking do SQL Server
            $updateVersion = EstoqueService::getLastVersionTrackingTable();

            //busco as ultimas alteraçẽos no ERP
            $dadosTrackingERP = EstoqueService::getLastChagingTrackingEstoque($lastVersion);
            //add/update na tabela "espelho"
            EstoqueService::flushEstoque($dadosTrackingERP);

            //atualiza na tabela de configurações
            EstoqueService::updateLastTrackingTable($updateVersion);

            dump('Last Execution: ' . (new \DateTime())->format('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
