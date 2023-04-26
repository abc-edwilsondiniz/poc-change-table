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
                        $table->id()->unique();
                        $table->integer('codpro');
                        $table->integer('codpro_tb_ct');
                        $table->string('dv');
                        $table->string('operation')->nullable(true);
                        $table->string('referencia', 17)->nullable(true);
                        $table->string('nome_original', 100)->nullable(true);
                        $table->string('ncm', 14)->nullable(true);
                        $table->string('modelo', 254);
                        $table->integer('venda_minima');
                        $table->string('codpro_fabricante', 25);
                        $table->string('un1', 3);
                        $table->string('un2', 3);
                        $table->decimal('faconv', 9);
                        $table->integer('cod_disponibilidade');
                        $table->string('disponibilidade', 254)->nullable(true);
                        $table->string('classe', 14);
                        $table->string('cod_classe', 14);
                        $table->string('n1', 25);
                        $table->string('n2', 25);
                        $table->string('n3', 25);
                        $table->integer('id_fornecedor');
                        $table->string('fornecedor')->nullable(true);
                        $table->string('estado_fornecedor_origem', 254)->nullable(true);
                        $table->decimal('altura', 9);
                        $table->decimal('largura', 9);
                        $table->decimal('peso', 8);
                        $table->decimal('comprimento', 9);
                        $table->decimal('custo_atual', 8);
                        $table->decimal('icms_ultima_compra', 8);
                        $table->string('data_ult_compra')->nullable(true);
                        $table->decimal('custo_ult_pesq', 13)->nullable(true);
                        $table->decimal('qtd_min_compra', 9);
                        $table->string('ean', 254);
                        $table->string('cf', 4);
                        $table->string('codigo_mens', 2);
                        $table->string('tributacao_mg')->nullable(true);
                        $table->string('origem', 1)->nullable(true);
                        $table->string('ref_end', 17);
                        $table->timestamps();
                        //unique
                        $table->unique(['codpro', 'dv']);
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
