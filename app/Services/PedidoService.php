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
                    p.numped,
                    p.codclie,
                    p.numorc,
                    p.codvend,
                    p.valped,
                    p.valentrad,
                    p.desconto,
                    p.dtprevent,
                    p.condpag,
                    p.dtpripag,
                    p.dtpedido,
                    p.obs,
                    p.numord,
                    p.tpo,
                    p.filial,
                    p.referencia,
                    p.moedcor,
                    p.dataatu,
                    p.ordprod,
                    p.perfrete,
                    p.valfrete,
                    p.perseguro,
                    p.valseguro,
                    p.razaocli,
                    p.endercli,
                    p.bairrcli,
                    p.cidadcli,
                    p.cepcli,
                    p.cgccli as cpf_cnpj,
                    p.inscli,
                    p.estcli,
                    p.tipnota,
                    p.codtran,
                    p.codtran2,
                    p.codendent,
                    p.codendcob,
                    p.observ,
                    p.filialcli,
                    p.respcli,
                    p.respfor,
                    p.tpoent,
                    p.situacao,
                    p.numpedfil,
                    p.atuarec,
                    p.numrom,
                    p.pendente,
                    p.libdesc,
                    p.libcred,
                    p.liblimi,
                    p.libbloq,
                    p.libatra,
                    p.sitven,
                    p.naoaprov,
                    p.telecli,
                    p.horaped,
                    p.freteorc,
                    p.numfrete,
                    p.usucred,
                    p.credito,
                    p.libform,
                    p.rformadepagar,
                    p.codlis,
                    p.tipofrete,
                    p.codrote,
                    p.SitConf,
                    p.NumClie,
                    p.ContribClie,
                    p.FilialVend,
                    p.destino,
                    p.CodMens,
                    p.Tributado,
                    p.inscsufracli,
                    p.COMPLCLI,
                    p.sitmanut,
                    p.codforout,
                    p.deporigem,
                    p.tpooutraent,
                    p.Cqualidade,
                    p.Refqualidade,
                    p.condpagposterior,
                    p.Etapa,
                    p.valorfat,
                    p.valorrec,
                    p.taxanf,
                    p.receber,
                    p.tipovenda,
                    p.oidrevenda,
                    p.sitmon,
                    p.temconjunto,
                    p.txrevenda,
                    p.SitCred,
                    p.FlagEmit,
                    p.sitsaga,
                    p.dtvalidade,
                    p.oidcontato,
                    p.LIBTPDOC,
                    p.Apelido,
                    p.contato,
                    p.PerDescto,
                    p.reservaconjunto,
                    p.viamont,
                    p.TemSeguro,
                    p.PLANOSEMJUROS,
                    p.rcarenciaparcela,
                    p.rdocplano,
                    p.rdocentrada,
                    p.ARQUIVOCDL,
                    p.FatorEmpresa,
                    p.Urgente,
                    p.PERCIPISEGURO,
                    p.OUTRASDESPESASINCLUSAS,
                    p.libmarkmin,
                    p.VIAORCAMENTO,
                    p.ViaOrdemSeparacao,
                    p.LIBPRAZOMEDIO,
                    p.LIBFRETE,
                    p.prazomedio,
                    p.desmembrado,
                    p.VALPEDTX,
                    p.ORGAOPUBCLIE,
                    p.ENDERECOFATURA,
                    p.CONTRATO,
                    p.EMPENHO,
                    cli.nome AS nome_cliente,
                    lower((select top 1 co.VALOR 
                          from COMUNICACAO_V co 
                          where co.RITEM = cli.oid and 
                               co.RTIPO = :rtipo)) as email_cliente
                FROM CHANGETABLE (CHANGES [PEDICLICAD], :lastVersion) AS c
                JOIN PEDICLICAD p on p.numped = c.numped
                JOIN clientecad cli on cli.oid = p.codclie', ['lastVersion' => $lastVersion, 'rtipo' => "32979"]);
        return json_decode(json_encode($dados), true);
    }

    /**
     * inserir/update os dados de pediclicad
     */
    public static function flushPediCliCad($dados) {

        Pedidos::upsert($dados, ['numped', 'filial'],
                [
                    "numped",
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
                    "endercli",
                    "bairrcli",
                    "cidadcli",
                    "cepcli",
                    "cpf_cnpj",
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
