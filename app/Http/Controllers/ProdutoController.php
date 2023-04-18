<?php

namespace App\Http\Controllers;

use App\Services\ProdutoService;

class ProdutoController extends Controller {

    public function produto() {
        try {
            //Busco na tabela de configurações a ultima versão que utilizamos
            $lastVersion = ProdutoService::getLastVersionControle();

            //Busco a última versão do change tracking do SQL Server
            $updateVersion = ProdutoService::getLastVersionTrackingTable();

            //busco as ultimas alteraçẽos no ERP
            $dadosProdutoTrackingERP = ProdutoService::getLastChagingTrackingProduto($lastVersion);

            //add/update na tabela "espelho produto"
            ProdutoService::flushProduto($dadosProdutoTrackingERP);

            //atualiza na tabela de configurações
            ProdutoService::updateLastTrackingTable($updateVersion);

            dump('Last Execution: ' . (new \DateTime())->format('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
