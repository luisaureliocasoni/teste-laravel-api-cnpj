<?php

namespace Tests\Feature;

use App\Models\Empresa;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpresaControllerTest extends TestCase
{

    use DatabaseMigrations, RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
    }

    protected function populateDatabase()
    {
        Empresa::insert([
            [
                "razao_social" => "MENEGHETTI INDUSTRIA QUIMICA LTDA",
                "nome_fantasia" => "",
                "atividade_principal" => "Fabricação de produtos de limpeza e polimento",
                "data_de_abertura" => "2003-07-04",
                "natureza_juridica" => "206-2 - Sociedade Empresária Limitada",
                "endereco_cep" => "17300000",
                "endereco_logradouro" => "R NICOLA OIOLI",
                "endereco_numero" => "210",
                "endereco_bairro" => "SETOR INDUSTRIAL",
                "endereco_complemento" => "QUADRA 01 - LOTE 17",
                "endereco_cidade" => "DOIS CORREGOS",
                "endereco_estado" => "SP",
                "cnpj" => "05753749000123",
                "endereco_pais" => "Brasil",
                "endereco_codigo_ibge" => "3514106",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                "razao_social" => "GUIGO TELEVISION LTD.",
                "nome_fantasia" => "GUIGO TV",
                "atividade_principal" => "Holdings de instituições não-financeiras",
                "data_de_abertura" => "2019-06-12",
                "natureza_juridica" => "221-6 - Empresa Domiciliada no Exterior",
                "endereco_cep" => "",
                "endereco_logradouro" => "FLOOR 4, WILLOW HOUSE, CRICKET SQUARE",
                "endereco_numero" => "S/N",
                "endereco_bairro" => "GEORGE TOWN",
                "endereco_complemento" => "KY1-9010",
                "endereco_cidade" => "EXTERIOR",
                "endereco_estado" => "EX",
                "cnpj" => "33913487000152",
                "endereco_pais" => "Exterior",
                "endereco_codigo_ibge" => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

    }

    public function test_verificando_cnpj_invalido()
    {
        $this->assertDatabaseCount('empresas', 0);

        $this->json('GET', 'api/empresa/33913487000150', [], ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertExactJson([
                "message" => "CNPJ inválido",
                "errors" => [
                    "cnpj" => [
                        "CNPJ inválido"
                    ]
                ]
            ]);

        $this->json('GET', 'api/empresa/111222333000099', [], ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertExactJson([
                "message" => "CNPJ inválido",
                "errors" => [
                    "cnpj" => [
                        "CNPJ inválido"
                    ]
                ]
            ]);

        $this->json('GET', 'api/empresa/3333', [], ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertExactJson([
                "message" => "CNPJ inválido",
                "errors" => [
                    "cnpj" => [
                        "CNPJ inválido"
                    ]
                ]
            ]);

        $this->assertDatabaseCount('empresas', 0);
    }

    public function test_inserindo_empresas_se_esta_funcionando()
    {
        $this->assertDatabaseMissing('empresas', [
            'cnpj' => '05753749000123'
        ]);

        $this->assertDatabaseCount('empresas', 0);

        $this->json('GET', 'api/empresa/05753749000123', [], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertExactJson([
                "razao_social" => "MENEGHETTI INDUSTRIA QUIMICA LTDA",
                "nome_fantasia" => "",
                "atividade_principal" => "Fabricação de produtos de limpeza e polimento",
                "data_de_abertura" => "2003-07-04",
                "natureza_juridica" => "206-2 - Sociedade Empresária Limitada",
                "endereco_cep" => "17300000",
                "endereco_logradouro" => "R NICOLA OIOLI",
                "endereco_numero" => "210",
                "endereco_bairro" => "SETOR INDUSTRIAL",
                "endereco_complemento" => "QUADRA 01 - LOTE 17",
                "endereco_cidade" => "DOIS CORREGOS",
                "endereco_estado" => "SP",
                "cnpj" => "05753749000123",
                "endereco_pais" => "Brasil",
                "endereco_codigo_ibge" => "3514106"
            ]);

        $this->assertDatabaseHas('empresas', [
            'cnpj' => '05753749000123'
        ]);

        $this->assertDatabaseCount('empresas', 1);

        $this->assertDatabaseMissing('empresas', [
            'cnpj' => '33913487000152'
        ]);

        $this->json('GET', 'api/empresa/33913487000152', [], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertExactJson( [
                "razao_social" => "GUIGO TELEVISION LTD.",
                "nome_fantasia" => "GUIGO TV",
                "atividade_principal" => "Holdings de instituições não-financeiras",
                "data_de_abertura" => "2019-06-12",
                "natureza_juridica" => "221-6 - Empresa Domiciliada no Exterior",
                "endereco_cep" => "",
                "endereco_logradouro" => "FLOOR 4, WILLOW HOUSE, CRICKET SQUARE",
                "endereco_numero" => "S/N",
                "endereco_bairro" => "GEORGE TOWN",
                "endereco_complemento" => "KY1-9010",
                "endereco_cidade" => "EXTERIOR",
                "endereco_estado" => "EX",
                "cnpj" => "33913487000152",
                "endereco_pais" => "Exterior"
            ]);

        $this->assertDatabaseHas('empresas', [
            'cnpj' => '33913487000152'
        ]);

        $this->assertDatabaseCount('empresas', 2);
    }


    public function test_consultando_empresa_existente_nao_insere_novo_dado() {
        $this->populateDatabase();
        $this->assertDatabaseCount('empresas', 2);

        $this->json('GET', 'api/empresa/05753749000123', [], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertExactJson([
                "cnpj" => "05753749000123",
                "razao_social" => "MENEGHETTI INDUSTRIA QUIMICA LTDA",
                "nome_fantasia" => "",
                "atividade_principal" => "Fabricação de produtos de limpeza e polimento",
                "data_de_abertura" => "2003-07-04",
                "natureza_juridica" => "206-2 - Sociedade Empresária Limitada",
                "endereco_cep" => "17300000",
                "endereco_codigo_ibge" => "3514106",
                "endereco_logradouro" => "R NICOLA OIOLI",
                "endereco_numero" => "210",
                "endereco_bairro" => "SETOR INDUSTRIAL",
                "endereco_complemento" => "QUADRA 01 - LOTE 17",
                "endereco_cidade" => "DOIS CORREGOS",
                "endereco_estado" => "SP",
                "endereco_pais" => "Brasil"
            ]);

        $this->assertDatabaseCount('empresas', 2);
    }


    public function test_verificando_cnpj_invalido_refresh_route()
    {
        $this->json('POST', 'api/empresa/33913487000150/refresh', [], ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertExactJson([
                "message" => "CNPJ inválido",
                "errors" => [
                    "cnpj" => [
                        "CNPJ inválido"
                    ]
                ]
            ]);

        $this->json('POST', 'api/empresa/3333/refresh', [], ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertExactJson([
                "message" => "CNPJ inválido",
                "errors" => [
                    "cnpj" => [
                        "CNPJ inválido"
                    ]
                ]
            ]);

    }

    public function test_verificando_se_esta_disparando_erro_404_cnpj_nao_cadastrado_refresh_route()
    {
        $this->populateDatabase();
        $this->assertDatabaseCount('empresas', 2);

        $response = $this->json('POST', 'api/empresa/33051491000159/refresh', [], ['Accept' => 'application/json'])
            ->assertStatus(404)
            ->getContent();

        $responseJson = json_decode($response, true);

        $this->assertEquals("Empresa não encontrada!", $responseJson['message']);
    }

    public function test_verificando_se_esta_atualizando_refresh_route()
    {
        $this->populateDatabase();
        $this->assertDatabaseCount('empresas', 2);

        // puxando os dados da empresa para ver se o timestamp está atualizando
        // emula dados desatualizados da empresa
        $empresaAntesUpdate = Empresa::find('33913487000152');
        $empresaAntesUpdate->nome_fantasia = "TESTE 123 - ISSO NÃO É UM NOME FANTASIA";
        $empresaAntesUpdate->save();

        $this->json('POST', 'api/empresa/33913487000152/refresh', [], ['Accept' => 'application/json'])
            ->assertStatus(200);

        $empresaDepoisUpdate = Empresa::find('33913487000152');

        $this->assertNotEquals($empresaAntesUpdate->updated_at->format('Y-m-d H:i:s'), $empresaDepoisUpdate->updated_at->format('Y-m-d H:i:s'));
        $this->assertNotEquals($empresaAntesUpdate->nome_fantasia, $empresaDepoisUpdate->nome_fantasia);

    }
}
