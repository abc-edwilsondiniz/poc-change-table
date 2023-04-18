<?php

namespace App\Services;

use App\Models\Configuracoes;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;

class ProdutoService {

    /**
     * retorna o ultimo valor q ainda não foi executado em busca dos trackings
     */
    public static function getLastVersionControle() {
        $lastVersion = Configuracoes::where('nome', 'change_tracking_produto')->first();

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
        $LastVersionTable = Configuracoes::where('nome', 'change_tracking_produto')->first();

        if (empty($LastVersionTable)) {
            $LastVersionTable = new Configuracoes();
            $LastVersionTable->nome = 'change_tracking_produto';
        }

        $LastVersionTable->valor = $version;
        $LastVersionTable->save();
    }

    /**
     * inserir/update os dados de estoque
     */
    public static function flushProduto($dados) {

        Produto::upsert($dados, ['codpro', 'dv', 'fornecedor'],
                [
                    "codpro",
                    "codpro-CT",
                    "dv",
                    "referencia",
                    "ncm",
                    "modelo",
                    "venda_minima",
                    "un1",
                    "id_categoria",
                    "nome_original",
                    "id_fornecedor",
                    "preco_tabela",
                    "altura",
                    "largura",
                    "peso",
                    "comprimento",
                    "custo_atual",
                    "custo_ult_pesq",
                    "qtd_min_compra",
                    "ean",
                    "fornecedor",
                    "raz_social",
                ]);
    }

    /**
     * busca as ultimas modificações da tabela no ERP
     */
    public static function getLastChagingTrackingProduto($lastVersion) {

        $dados = DB::connection('sqlsrv_ERP')->select(
                    "SELECT
                        Pro.Codpro                                    AS 'codpro',
                        ct.codpro									  AS 'codpro-CT',
                        ct.dv										  AS 'dv',
                        TRIM(CONCAT(Pro.codinterno,''))               AS 'referencia',
                        TRIM(pro.codigoncm)                           AS 'ncm',
                        TRIM(Pro.modelo)                              AS 'modelo',
                        CONCAT(cmp.vendaminima, '')                   AS 'venda_minima',
                        CONCAT(cmp.CODPROFABRICANTE,'')               AS 'codprofabricante',
                        Pro.unid1                                     AS 'un1',
                        SUBSTRING(pro.clasprod,1,6)                   AS 'id_categoria',
                        cmp.descricaolonga                            AS 'nome_original',
                        pro.codfor                                    AS 'id_fornecedor',
                        0                                             AS 'preco_tabela',
                        TRIM(CONCAT(COALESCE(cmp.alturacm,0),''))     AS 'altura',
                        TRIM(CONCAT(COALESCE(cmp.larguracm,0),''))    AS 'largura',
                        TRIM(CONCAT(COALESCE(pro.pesounit,0),''))     AS 'peso',
                        TRIM(CONCAT(COALESCE(cmp.comprimentocm,0),''))AS 'comprimento',
                        CONCAT(pro.precocomp,'')                      AS 'custo_atual',
                        CONCAT((ISNULL((SELECT top 1 valorcusto FROM pesquisa WHERE codigoexterno = pro.codpro ORDER BY criadoem DESC),0)),'') AS 'custo_ult_pesq',
                        CONCAT((SELECT qtmincompr FROM itemfilest WHERE filial = '10' AND codpro = pro.codpro), '') AS 'qtd_min_compra',
                        TRIM(CONCAT((SELECT top 1 referencia FROM prodrefcad WHERE codpro = pro.codpro AND CodigoBarraEAN IS NOT NULL AND CodigoBarraEAN NOT IN ('')), '')) AS 'ean',
                        fnd.NOME AS 'fornecedor',
                        fnd.RAZSOC AS 'raz_social'
                    FROM CHANGETABLE (CHANGES [PRODUTOCAD], :lastVersion) AS ct
                    INNER JOIN produtocad pro on pro.codpro = ct.codpro and pro.dv = ct.dv
                    INNER JOIN complementoproduto    cmp ON pro.codpro = cmp.codpro
                    INNER JOIN fornececad            fnd ON pro.codfor = fnd.oid
                    INNER JOIN item                  ite ON pro.disponibilidade = ite.oid
                    LEFT JOIN  SKU_PRODUTO_ECOMMERCE sku ON sku.REFERENCIA=pro.codinterno"
                    ,['lastVersion' => $lastVersion]);

        return json_decode(json_encode($dados), true);
    }

}
