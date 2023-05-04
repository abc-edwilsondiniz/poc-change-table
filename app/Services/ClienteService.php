<?php

namespace App\Services;

use App\Models\Clientes;

class ClienteService {

    /**
     * inserir/update os dados de clientes
     */
    public static function flushClientes($dados) {

        Clientes::upsert(
                $dados,
                ['cpf_cnpj'],
                ["cpf_cnpj",
                "nome",
                "razao_social",
                "email",
                "celular",
        ]);
    }

}
