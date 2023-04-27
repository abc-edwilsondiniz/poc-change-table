<?php

namespace App\Services;

use App\Models\Configuracoes;
use App\Models\Pedidos;
use Illuminate\Support\Facades\DB;

class PedidoService {

    /**
     * retorna o ultimo valor q ainda não foi executado em busca dos trackings
     */
    public static function getLastVersionControle() {
        $lastVersion = Configuracoes::where('nome', 'change_tracking_pedido')->first();

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
        $LastVersionTable = Configuracoes::where('nome', 'change_tracking_pedido')->first();

        if (empty($LastVersionTable)) {
            $LastVersionTable = new Configuracoes();
            $LastVersionTable->nome = 'change_tracking_pedido';
        }

        $LastVersionTable->valor = $version;
        $LastVersionTable->save();
    }

    /**
     * busca as ultimas modificações da tabela no ERP
     */
    public static function getLastChagingTrackingERP($lastVersion) {

        $dados = DB::connection('sqlsrv_ERP')->select(
                'SELECT
                        p.*
                    FROM CHANGETABLE (CHANGES [PEDICLICAD], :lastVersion) AS c
                    JOIN PEDICLICAD p on p.numped = c.numped
                    ORDER BY SYS_CHANGE_VERSION', ['lastVersion' => $lastVersion]);
        return json_decode(json_encode($dados), true);
    }

    /**
     * inserir/update os dados de pediclicad
     */
    public static function flushPediCliCad($dados) {

        Pedidos::upsert($dados, ['numped', 'filial'], ["numped",
            "codclie",
            "numorc",
            "codvend",
            "valped",
            "valentrad",
            "desconto",
            "dtprevent",
            "condpag",
            "dtpripag",
            "dtpedido",
            "obs",
            "numord",
            "tpo",
            "filial",
            "referencia",
            "moedcor",
            "dataatu",
            "ordprod",
            "perfrete",
            "valfrete",
            "perseguro",
            "valseguro",
            "razaocli",
            "endercli",
            "bairrcli",
            "cidadcli",
            "cepcli",
            "cgccli",
            "inscli",
            "estcli",
            "tipnota",
            "codtran",
            "codtran2",
            "codendent",
            "codendcob",
            "observ",
            "filialcli",
            "respcli",
            "respfor",
            "tpoent",
            "situacao",
            "numpedfil",
            "atuarec",
            "numrom",
            "pendente",
            "libdesc",
            "libcred",
            "liblimi",
            "libbloq",
            "libatra",
            "sitven",
            "naoaprov",
            "telecli",
            "horaped",
            "freteorc",
            "numfrete",
            "usucred",
            "credito",
            "libform",
            "rformadepagar",
            "codlis",
            "tipofrete",
            "codrote",
            "SitConf",
            "NumClie",
            "ContribClie",
            "FilialVend",
            "destino",
            "CodMens",
            "Tributado",
            "inscsufracli",
            "COMPLCLI",
            "sitmanut",
            "codforout",
            "deporigem",
            "tpooutraent",
            "Cqualidade",
            "Refqualidade",
            "condpagposterior",
            "Etapa",
            "valorfat",
            "valorrec",
            "taxanf",
            "receber",
            "tipovenda",
            "oidrevenda",
            "sitmon",
            "temconjunto",
            "txrevenda",
            "SitCred",
            "FlagEmit",
            "sitsaga",
            "dtvalidade",
            "oidcontato",
            "LIBTPDOC",
            "Apelido",
            "contato",
            "PerDescto",
            "reservaconjunto",
            "viamont",
            "TemSeguro",
            "PLANOSEMJUROS",
            "rcarenciaparcela",
            "rdocplano",
            "rdocentrada",
            "ARQUIVOCDL",
            "FatorEmpresa",
            "Urgente",
            "PERCIPISEGURO",
            "OUTRASDESPESASINCLUSAS",
            "libmarkmin",
            "VIAORCAMENTO",
            "ViaOrdemSeparacao",
            "LIBPRAZOMEDIO",
            "LIBFRETE",
            "prazomedio",
            "desmembrado",
            "VALPEDTX",
            "ORGAOPUBCLIE",
            "ENDERECOFATURA",
            "CONTRATO",
            "EMPENHO"
        ]);
    }

}
