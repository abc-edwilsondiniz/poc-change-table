<?php

namespace App\Services;

use App\Models\Configuracoes;
use App\Models\Estoque;
use Illuminate\Support\Facades\DB;

class EstoqueService {

    /**
     * retorna o ultimo valor q ainda não foi executado em busca dos trackings
     */
    public static function getLastVersionControleEstoqueAtual() {
        $lastVersion = Configuracoes::where('nome', 'change_tracking_estoque_atual')->first();

        //ainda não tem versao na tabela de controle
        if (empty($lastVersion)) {
            $version = DB::connection('sqlsrv_ERP')->selectOne('select CHANGE_TRACKING_CURRENT_VERSION() as version');
            return $version->version;
        }

        return $lastVersion->valor;
    }

    /**
     * retorna o ultimo valor q ainda não foi executado em busca dos trackings
     */
    public static function getLastVersionControleEstoqueFuturo() {
        $lastVersion = Configuracoes::where('nome', 'change_tracking_estoque_futuro')->first();

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
    public static function updateLastTrackingTableEstoqueAtual($version) {

        //atualiza a ultima versao na tabela de controle
        $LastVersionTable = Configuracoes::where('nome', 'change_tracking_estoque_atual')->first();

        if (empty($LastVersionTable)) {
            $LastVersionTable = new Configuracoes();
            $LastVersionTable->nome = 'change_tracking_estoque_atual';
        }

        $LastVersionTable->valor = $version;
        $LastVersionTable->save();
    }

    /**
     * atualiza o valor na tabela de controle
     */
    public static function updateLastTrackingTableEstoqueFuturo($version) {

        //atualiza a ultima versao na tabela de controle
        $LastVersionTable = Configuracoes::where('nome', 'change_tracking_estoque_futuro')->first();

        if (empty($LastVersionTable)) {
            $LastVersionTable = new Configuracoes();
            $LastVersionTable->nome = 'change_tracking_estoque_futuro';
        }

        $LastVersionTable->valor = $version;
        $LastVersionTable->save();
    }

    /**
     * inserir/update os dados de estoque
     */
    public static function flushEstoqueAtual($dados) {

        Estoque::upsert($dados, ['codpro', 'dv', 'filial'],
                [
                    "codpro",
                    "dv",
                    "referencia",
                    "estoque_atual",
                    "filial",
        ]);
    }

    /**
     * inserir/update os dados de estoque
     */
    public static function flushEstoqueFuturo($dados) {

        Estoque::upsert($dados, ['codpro', 'dv', 'filial'],
                [
                    "codpro",
                    "dv",
                    "referencia",
                    "estoque_futuro",
                    "filial",
        ]);
    }

    /**
     * busca as ultimas modificações da tabela no ERP
     */
    public static function getLastChagingTrackingEstoqueAtual($lastVersion) {

        $dados = DB::connection('sqlsrv_ERP')->select(
                "SELECT 
                    pro.codpro,
                    pro.dv,
                    TRIM(pro.codinterno) AS 'referencia',
                    TRIM(CONCAT((i.quant - i.qtdereserv), '')) AS 'estoque_atual',
                    i.filial 
                FROM
                    CHANGETABLE (CHANGES [ITEMFILEST], :lastVersion) AS ct
                INNER JOIN itemfilest i ON i.codpro = ct.codpro and i.filial = ct.filial
                INNER JOIN produtocad pro ON pro.codpro = i.codpro AND pro.dv = i.dv 
                WHERE (i.quant - i.qtdereserv) > 0", ['lastVersion' => $lastVersion]);
        
        return json_decode(json_encode($dados), true);
    }
    
    /**
     * busca as ultimas modificações da tabela no ERP
     */
    public static function getLastChagingTrackingEstoqueFuturo($lastVersion) {

        $dados = DB::connection('sqlsrv_ERP')->select(
                " SELECT 
                        pro.codpro,
                        pro.dv,
                        TRIM(pro.codinterno) AS 'referencia',
                        (SUM(i.quant) - SUM(i.quantrec)) as 'estoque_futuro',
                        i.filial 
                    FROM
                        CHANGETABLE (CHANGES [itemforcad], :lastVersion) AS ct
                    INNER JOIN itemforcad i ON i.codpro = ct.codpro AND i.numped = ct.numped
                    INNER JOIN produtocad pro ON pro.codpro = ct.codpro AND pro.dv = i.dv
                    WHERE i.quantrec <> i.quant
                    GROUP BY pro.codpro,
                        pro.dv,
                        pro.codinterno,
                        i.filial", ['lastVersion' => $lastVersion]);
        
        return json_decode(json_encode($dados), true);
    }
   
}
