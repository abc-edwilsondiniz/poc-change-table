<?php

namespace App\Services;

use App\Models\Configuracoes;
use App\Models\PediClicad;
use App\Models\Estoque;
use Illuminate\Support\Facades\DB;

class EstoqueService {

    /**
     * retorna o ultimo valor q ainda não foi executado em busca dos trackings
     */
    public static function getLastVersionControle() {
        $lastVersion = Configuracoes::where('nome', 'change_tracking_estoque')->first();

        //ainda não tem versao na tabela de controle
        if (empty($lastVersion)) {
            $version = DB::connection('sqlsrv_ERP')->selectOne('select CHANGE_TRACKING_CURRENT_VERSION() as version');
            return $version->version;
        }

        return $lastVersion->valor;
    }

    /**
     * retorna o ultimo tracking para a proximo controle interno da execuçao
     */
    public static function getLastVersionTrackingTable() {

        $version = DB::connection('sqlsrv_ERP')->selectOne('select CHANGE_TRACKING_CURRENT_VERSION() as version');
        return $version->version;
    }

    /**
     * atualiza o valor na tabela de controle
     */
    public static function updateLastTrackingTable($version) {

        //atualiza a ultima versao na tabela de controle
        $LastVersionTable = Configuracoes::where('nome', 'change_tracking_estoque')->first();

        if (empty($LastVersionTable)) {
            $LastVersionTable = new Configuracoes();
            $LastVersionTable->nome = 'change_tracking_estoque';
        }

        $LastVersionTable->valor = $version;
        $LastVersionTable->save();
    }

    /**
     * inserir/update os dados de estoque
     */
    public static function flushEstoque($dados) {

        Estoque::upsert($dados, ['codpro', 'dv', 'filial'],
                [
                    "codpro",
                    "dv",
                    "referencia",
                    "quantidade",
                    "filial",
        ]);
    }

    /**
     * busca as ultimas modificações da tabela no ERP
     */
    public static function getLastChagingTrackingEstoque($lastVersion) {

        $dados = DB::connection('sqlsrv_ERP')->select(
                "SELECT
                    pro.codpro,
                    pro.dv,
                    TRIM(pro.codinterno) AS 'referencia',
                    TRIM(CONCAT((i.quant - i.qtdereserv), '')) AS 'quantidade',
                    i.filial 
                FROM
                    CHANGETABLE (CHANGES [PRODUTOCAD], :lastVersion) AS ct
                INNER JOIN produtocad pro ON pro.codpro = ct.codpro AND pro.dv = ct.dv 
                INNER JOIN itemfilest i ON i.codpro = pro.codpro
                WHERE (i.quant - i.qtdereserv) > 0", ['lastVersion' => $lastVersion]);
        return json_decode(json_encode($dados), true);
    }

}
