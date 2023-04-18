<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('produto', function (Blueprint $table) {
            $table->id();
            $table->integer('codpro');
            $table->integer('codpro-CT');
            $table->string('dv');
            $table->string('referencia');
            $table->string('ncm');
            $table->string('modelo');
            $table->string('venda_minima');
            $table->string('codprofabricante');
            $table->string('un1');
            $table->string('id_categoria');
            $table->string('nome_original');
            $table->integer('id_fornecedor');
            $table->integer('preco_tabela');
            $table->string('altura');
            $table->string('largura');
            $table->string('peso');
            $table->string('comprimento');
            $table->string('custo_atual');
            $table->string('custo_ult_pesq');
            $table->string('qtd_min_compra');
            $table->string('ean');
            $table->string('fornecedor');
            $table->string('raz_social');
            $table->timestamps();
            //unique
            $table->unique(['codpro', 'dv', 'fornecedor']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('produto');
    }
};
