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
        Schema::create('estoque', function (Blueprint $table) {
            $table->id();
            $table->integer('codpro');
            $table->string('dv');
            $table->string('fornecedor');
            $table->string('referencia');
            $table->string('codProFabricante')->nullable(true);
            $table->string('disponibilidade');
            $table->smallInteger('prazo');
            $table->decimal('quantidade', 8, 3);
            $table->integer('numeroPedido')->nullable(true);
            $table->decimal('quantidadeRecebida', 8, 3)->nullable(true);
            $table->date('dataPrevisaoRecebimento')->nullable(true);
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
        Schema::dropIfExists('estoque');
    }
};
