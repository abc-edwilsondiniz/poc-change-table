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
        Schema::create('estoques', function (Blueprint $table) {
            $table->id();
            $table->integer('codpro');
            $table->string('dv');
            $table->smallInteger('filial');
            $table->string('referencia');
            $table->decimal('estoque_atual', 8, 3)->nullable(true);
            $table->decimal('estoque_futuro', 8, 3)->nullable(true);
            $table->timestamps();

            //unique
            $table->unique(['codpro', 'dv', 'filial']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('estoques');
    }
};
