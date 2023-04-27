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
        Schema::create('produtos', function (Blueprint $table) {
                        $table->id()->unique();
                        $table->integer('codpro');
                        $table->integer('codpro_tb_ct')->nullable(true);
                        $table->string('dv')->nullable(true);
                        $table->string('operation')->nullable(true);
                        $table->string('referencia', 17)->nullable(true);
                        $table->string('nome_original', 100)->nullable(true);
                        $table->string('ncm', 14)->nullable(true);
                        $table->string('modelo', 254)->nullable(true);
                        $table->integer('venda_minima')->nullable(true);
                        $table->string('codpro_fabricante', 25)->nullable(true);
                        $table->string('un1', 3)->nullable(true);
                        $table->string('un2', 3)->nullable(true);
                        $table->decimal('faconv', 9)->nullable(true);
                        $table->integer('cod_disponibilidade')->nullable(true);
                        $table->string('disponibilidade', 254)->nullable(true);
                        $table->string('classe', 14)->nullable(true);
                        $table->string('cod_classe', 14)->nullable(true);
                        $table->string('n1', 25)->nullable(true);
                        $table->string('n2', 25)->nullable(true);
                        $table->string('n3', 25)->nullable(true);
                        $table->integer('id_fornecedor')->nullable(true);
                        $table->string('fornecedor')->nullable(true);
                        $table->string('estado_fornecedor_origem', 254)->nullable(true);
                        $table->decimal('altura', 9)->nullable(true);
                        $table->decimal('largura', 9)->nullable(true);
                        $table->decimal('peso', 8)->nullable(true);
                        $table->decimal('comprimento', 9)->nullable(true);
                        $table->decimal('custo_atual', 8)->nullable(true);
                        $table->decimal('icms_ultima_compra', 8)->nullable(true);
                        $table->string('data_ult_compra')->nullable(true)->nullable(true);
                        $table->decimal('custo_ult_pesq', 13)->nullable(true);
                        $table->decimal('qtd_min_compra', 9)->nullable(true);
                        $table->string('ean', 254)->nullable(true);
                        $table->string('cf', 4)->nullable(true);
                        $table->string('codigo_mens', 2)->nullable(true);
                        $table->string('tributacao_mg')->nullable(true);
                        $table->string('origem', 1)->nullable(true);
                        $table->string('ref_end', 17)->nullable(true);
                        $table->string('origem_traking', 254)->nullable(true);
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
        Schema::dropIfExists('produtos');
    }
};
