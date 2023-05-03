<?php

namespace App\Http\Controllers;

use App\Services\PedidoService;
use App\Services\ClienteService;
use App\Services\EnderecoClienteService;

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
            $enderecoCliente = [];
            foreach ($chunks as $chunk) {
                foreach ($chunk as $key => $value) {
                    $clientes[$key] = [
                        'cpf_cnpj' => trim($value['cpf_cnpj']),
                        'nome' => trim($value['nome_cliente']),
                        'razao_social' => trim($value['razaocli']),
                        'email' => trim($value['email_cliente']),
                        'celular' => trim($value['telecli']),
                    ];

                    $enderecoCliente[$key] = [
                        'cpf_cnpj' => trim($value['cpf_cnpj']),
                        'logradouro' => trim($value['endercli']),
                        'numero' => trim($value['NumClie']),
                        'bairro' => trim($value['bairrcli']),
                        'cidade' => trim($value['cidadcli']),
                        'cep' => trim($value['cepcli']),
                        'uf' => trim($value['estcli']),
                        'complemento' => trim($value['COMPLCLI']),
                        'contato' => trim($value['contato']),
                    ];

                    //remover os campos que não existem na tabela de pedido
                    unset($chunk[$key]['nome_cliente']);
                    unset($chunk[$key]['email_cliente']);
                    unset($chunk[$key]['razaocli']);
                    unset($chunk[$key]['telecli']);
                }//FIM FOREACH CHUNCK INTERNO

                /* add/update na tabela "espelho" */
                ClienteService::flushClientes($clientes);

                //add/update na tabela "espelho"
                EnderecoClienteService::flushEndereco($enderecoCliente);

                //add/update na tabela "espelho"
                PedidoService::flushPediCliCad($chunk);
            }//FIM FOREACH CHUNK EXTERNO

            /* atualiza na tabela de configurações */
            PedidoService::updateLastTrackingTable($updateVersion);

            dump('Last Execution: ' . (new \DateTime())->format('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
