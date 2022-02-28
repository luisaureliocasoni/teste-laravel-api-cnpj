<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{

    // Atributos de conexão a tabela
    protected $primaryKey = 'cnpj';
    protected $table      = 'empresas';
    protected $hidden     = [
        'created_at',
        'updated_at'
    ];
    protected $fillable   = [
        'cnpj',
        'razao_social',
        'nome_fantasia',
        'atividade_principal',
        'data_de_abertura',
        'natureza_juridica',
        'endereco_cep',
        'endereco_codigo_ibge',
        'endereco_logradouro',
        'endereco_bairro',
        'endereco_cidade',
        'endereco_estado',
        'endereco_pais',
    ];


}
