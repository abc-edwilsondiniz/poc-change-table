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

        Estoque::upsert($dados, ['codProFabricante', 'dv', 'fornecedor'],
                [
                    "codpro",
                    "dv",
                    "fornecedor",
                    "referencia",
                    "codProFabricante",
                    "dv",
                    "disponibilidade",
                    "prazo",
                    "quantidade",
                    "numeroPedido",
                    "quantidadeRecebida",
                    "dataPrevisaoRecebimento",
        ]);
    }

    /**
     * busca as ultimas modificações da tabela no ERP
     */
    public static function getLastChagingTrackingEstoque($lastVersion) {

        $dados = DB::connection('sqlsrv_ERP')->select(
                "SELECT
            ct.codpro,
            ct.dv,
            'ABC - CD JUIZ DE FORA' AS 'fornecedor',
            TRIM(pro.codinterno) AS 'referencia',
            pro.codpro AS 'codProFabricante',
            'PRONTA ENTREGA' AS 'disponibilidade',
            0 AS 'prazo',
            TRIM(CONCAT((SELECT (quant - qtdereserv) FROM itemfilest WHERE filial = '10' AND codpro = pro.codpro ), '')) AS 'quantidade',
            null as 'numeroPedido',
            null as 'quantidadeRecebida',
            null as 'dataPrevisaoRecebimento'
        FROM
                CHANGETABLE (CHANGES [PRODUTOCAD], :lastVersion) AS ct
        INNER JOIN produtocad pro on pro.codpro = ct.codpro and pro.dv = ct.dv 
        INNER JOIN complementoproduto cmp ON pro.codpro = cmp.codpro
        INNER JOIN fornececad fnd ON pro.codfor = fnd.oid
        INNER JOIN item ite ON pro.disponibilidade = ite.oid
        WHERE
                (SELECT (quant - qtdereserv) FROM itemfilest WHERE filial = '10' AND codpro = pro.codpro) > 0
        UNION ALL
        SELECT
            ct.codpro,
            ct.dv,
            'ABC - CAMINHÕES' AS 'fornecedor',
            TRIM(CONCAT(pro.codinterno,'')) AS 'referencia',
            null AS 'codProFabricante',
            'EM TRANSITO' AS 'disponibilidade',
            0 AS 'prazo',
            TRIM(CONCAT(i.quant,'')) AS 'quantidade',
            TRIM(CONCAT(i.numped,'')) AS 'numeroPedido',
            TRIM(CONCAT(i.quantrec,'')) AS 'quantidadeRecebida',
            i.dtprevrec AS 'dataPrevisaoRecebimento'
        FROM CHANGETABLE (CHANGES [PRODUTOCAD], :lastVersion2) AS ct
        INNER JOIN produtocad pro on pro.codpro = ct.codpro and pro.dv = ct.dv 
        INNER JOIN itemforcad i ON i.codpro = pro.codpro
        WHERE
            i.filial='10'
            and i.quantrec <> i.quant", ['lastVersion' => $lastVersion, 'lastVersion2' => $lastVersion]);
        return json_decode(json_encode($dados), true);
    }

}
