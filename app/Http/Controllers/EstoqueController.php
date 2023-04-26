<?php

namespace App\Http\Controllers;

use App\Services\EstoqueService;
use App\Jobs\CargaEstoqueAtualJob;

class EstoqueController extends Controller {

    public function estoque() {
        try {
            //------------------------------------------------------------------
            //ESTOQUE ATUAL
            //------------------------------------------------------------------
            //Busco na tabela de configurações a ultima versão que utilizamos
            $lastVersion = EstoqueService::getLastVersionControleEstoqueAtual();

            //Busco a última versão do change tracking do SQL Server
            $updateVersion = EstoqueService::getLastVersionTrackingTable();

            //busco as ultimas alteraçẽos no ERP
            $dadosTrackingERP = EstoqueService::getLastChagingTrackingEstoqueAtual($lastVersion);

            $chunks = array_chunk($dadosTrackingERP, 500);
            foreach ($chunks as $chunk) {
                //add/update na tabela "espelho"
                EstoqueService::flushEstoqueAtual($chunk);
            }

            //atualiza na tabela de configurações
            EstoqueService::updateLastTrackingTableEstoqueAtual($updateVersion);

            //------------------------------------------------------------------
            //ESTOQUE FUTURO
            //------------------------------------------------------------------
            //Busco na tabela de configurações a ultima versão que utilizamos
            $lastVersionFuturo = EstoqueService::getLastVersionControleEstoqueFuturo();

            //Busco a última versão do change tracking do SQL Server
            $updateVersionFuturo = EstoqueService::getLastVersionTrackingTable();

            //busco as ultimas alteraçẽos no ERP
            $dadosTrackingERPFuturo = EstoqueService::getLastChagingTrackingEstoqueFuturo($lastVersionFuturo);

            $chunks2 = array_chunk($dadosTrackingERPFuturo, 500);
            foreach ($chunks2 as $chunk) {
                //add/update na tabela "espelho"
                EstoqueService::flushEstoqueFuturo($chunk);
            }

            //atualiza na tabela de configurações
            EstoqueService::updateLastTrackingTableEstoqueFuturo($updateVersionFuturo);

            dump('Last Execution: ' . (new \DateTime())->format('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
