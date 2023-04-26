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
            $chunks = array_chunk($dadosProdutoTrackingERP, 500); // limita a carga da consulta em 500 registros por vez
            foreach ($chunks as $chunk) {
                //add/update na tabela "espelho produto"
                ProdutoService::flushProduto($chunk);
            }
            //atualiza na tabela de configurações
            ProdutoService::updateLastTrackingProdutoTable($updateVersionProduct);

            //--------------------------------------------------------------//
            //      complements off product
            //--------------------------------------------------------------//
            //Busco na tabela de configurações a ultima versão que utilizamos
            $lastVersionProdutoComplemento = ProdutoService::getLastVersionProdutoComplementoControle();

            //Busco a última versão do change tracking do SQL Server
            $updateVersionComplement = ProdutoService::getLastVersionTrackingTable();

            //busco as ultimas alteraçẽos de complemento do produto no ERP
            $dadosProdutoComplementoTrackingERP = ProdutoService::getLastChagingTrackingProdutoComplemento($lastVersionProdutoComplemento);

            $chunksComp = array_chunk($dadosProdutoComplementoTrackingERP, 500); // limita a carga da consulta em 500 registros por vez
            foreach ($chunksComp as $chunkComp) {
                //add/update na tabela "espelho produto"
                ProdutoService::flushProdutoComplemento($chunkComp);
            }
            //atualiza na tabela de configurações
            ProdutoService::updateLastTrackingProdutoComplementoTable($updateVersionComplement);

            dump('Last Execution: ' . (new \DateTime())->format('Y-m-d H:i:s'));

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
