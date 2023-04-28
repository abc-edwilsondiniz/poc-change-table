<?php

namespace App\Http\Controllers;

use App\Services\PedidoService;
use App\Services\ClienteService;

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

            $clientes = [];
            foreach ($chunks as $chunk) {
                foreach ($chunk as $key => $value) {
                    $clientes[$key] = [
                        'cpf_cnpj' => trim($value['cpf_cnpj']),
                        'nome' => trim($value['nome_cliente']),
                        'razao_social' => trim($value['razaocli']),
                        'email' => trim($value['email_cliente']),
                        'celular' => trim($value['telecli']),
                    ];

                    //remover os campos pra nao dar erro no flush de pedido
                    unset($chunk[$key]['nome_cliente']);
                    unset($chunk[$key]['email_cliente']);
                    unset($chunk[$key]['razaocli']);
                    unset($chunk[$key]['telecli']);
                }

                ClienteService::flushClientes($clientes);

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
