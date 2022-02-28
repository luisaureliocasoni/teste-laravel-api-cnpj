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
        Schema::create('empresas', function (Blueprint $table) {
            $table->string('cnpj', '14')->primary();
            $table->string('razao_social', 255);
            $table->string('nome_fantasia', 255);
            $table->string('atividade_principal', 500);
            $table->date('data_de_abertura');
            $table->string('natureza_juridica', 255);
            $table->string('endereco_cep', 8)->nullable();
            $table->string('endereco_codigo_ibge', 8)->nullable();
            $table->string('endereco_logradouro', 255);
            $table->string('endereco_numero', 255);
            $table->string('endereco_bairro', 255)->nullable();
            $table->string('endereco_complemento', 255)->nullable();
            $table->string('endereco_cidade', 255)->nullable();
            $table->string('endereco_estado', 255)->nullable();
            $table->string('endereco_pais', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresas');
    }
};
