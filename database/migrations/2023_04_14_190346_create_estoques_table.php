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
            $table->string('referencia');
            $table->decimal('quantidade', 8, 3);
            $table->smallInteger('filial');
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
        Schema::dropIfExists('estoque');
    }
};
