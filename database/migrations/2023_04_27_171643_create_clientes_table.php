<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('cpf_cnpj', 18);
            $table->string('nome', 150);
            $table->string('razao_social', 150)->nullable(true);
            $table->string('email', 150)->nullable(true);
            $table->string('celular', 20)->nullable(true);
            $table->timestamps();
            //unique
            $table->unique(['cpf_cnpj']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};
