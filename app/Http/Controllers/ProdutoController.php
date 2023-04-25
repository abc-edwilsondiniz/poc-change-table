<?php

namespace App\Http\Controllers;

use App\Services\ProdutoService;

class ProdutoController extends Controller {

    public function produto() {
        try {
            //--------------------------------------------------------------//
            //      data off product
            //--------------------------------------------------------------//
            //Busco na tabela de configurações a ultima versão que utilizamos
            $lastVersionProduto = ProdutoService::getLastVersionProdutoControle();

            //Busco a última versão do change tracking do SQL Server
            $updateVersionProduct = ProdutoService::getLastVersionTrackingTable();

            //busco as ultimas alteraçẽos no ERP
            $dadosProdutoTrackingERP = ProdutoService::getLastChagingTrackingProduto($lastVersionProduto);
            // dd($dadosProdutoTrackingERP);
            //add/update na tabela "espelho produto"
            ProdutoService::flushProduto($dadosProdutoTrackingERP);

            //atualiza na tabela de configurações
            ProdutoService::updateLastTrackingProdutoTable($updateVersionProduct);


            //--------------------------------------------------------------//
            //      complements off product
            //--------------------------------------------------------------//
            //Busco na tabela de configurações a ultima versão que utilizamos
            // $lastVersionProdutoComplemento = ProdutoService::getLastVersionProdutoComplementoControle();



            dump('Last Execution: ' . (new \DateTime())->format('Y-m-d H:i:s'));

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
