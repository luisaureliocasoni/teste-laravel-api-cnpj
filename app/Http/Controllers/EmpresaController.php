<?php

namespace App\Http\Controllers;


use DateTime;
use Illuminate\Http\Request;
use App\Models\Empresa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EmpresaController extends Controller
{

    public function get($cnpj, Request $request)
    {

        // Tenta validar se o CNPJ é válido, para evitar problemas de requisição
        $request->merge(['cnpj' => $cnpj]);
        $this->validate($request, [
            'cnpj' => 'required|cnpj',
        ]);

        // Tenta recuperar a empresa direto no banco de dados
        $empresa_db = Empresa::find($cnpj);

        if ($empresa_db){
            // achou a empresa - retorna direto
            return response()->json($empresa_db);
        }

        $empresa_nova = $this->getOnIRS($cnpj);

        if ($empresa_nova === false) {
            return response()->json([
                'message' => 'CNPJ não encontrado ou estamos com dificuldades técnicas. Revise o CNPJ e tente novamente.'
            ], 500);
        }


        // puxa o codigo IBGE da cidade - estado
        if ($empresa_nova->endereco_estado === 'EX') {
            // a api não indica o País, logo terei que colocar que é no exterior
            $empresa_nova->endereco_pais = 'Exterior';
        } else {
            $codigo_ibge = $this->getIBGECodeCity( $empresa_nova->endereco_estado,  $empresa_nova->endereco_cidade );

            if ($codigo_ibge === false) {
                return response()->json([
                    'message' => 'Dificuldades técnicas para consultar a API do IBGE. Tente novamente mais tarde.'
                ], 500);
            } else if ($codigo_ibge === null) {
                return response()->json([
                    'message' => 'Erro fatal: Cidade '.$empresa_nova->endereco_cidade.'-'.$empresa_nova->endereco_estado.' não encontrada na lista do IBGE.'
                ], 500);
            }

            $empresa_nova->endereco_pais = 'Brasil';
            $empresa_nova->endereco_codigo_ibge = $codigo_ibge;
        }

        // salva a empresa
        $empresa_nova->save();

        return response()->json($empresa_nova);
    }


    /**
     * Recupera os aados do CNPJ na Receita Federal
     * @param $cnpj
     * @return Empresa|false
     */
    private function getOnIRS($cnpj) {
        try{
            $response = Http::get('https://receitaws.com.br/v1/cnpj/'.$cnpj);
            $responseData = $response->json();

            if (isset($responseData['status']) === false || $responseData['status'] !== 'OK'){
                // houve um erro na consulta
                return false;
            }

            $empresa = new Empresa;
            $empresa->razao_social = $responseData['nome'];
            $empresa->nome_fantasia = $responseData['fantasia'];
            $empresa->atividade_principal = $responseData['atividade_principal'][0]['text'];
            $empresa->data_de_abertura = (DateTime::createFromFormat('d/m/Y', $responseData['abertura']))->format('Y-m-d');
            $empresa->natureza_juridica = $responseData['natureza_juridica'];
            $empresa->endereco_cep = str_replace(['.', '-'], ['', ''], $responseData['cep']);
            $empresa->endereco_logradouro = $responseData['logradouro'];
            $empresa->endereco_numero = $responseData['numero'];
            $empresa->endereco_bairro = $responseData['bairro'];
            $empresa->endereco_complemento = $responseData['complemento'];
            $empresa->endereco_cidade = $responseData['municipio'];
            $empresa->endereco_estado = $responseData['uf'];
            $empresa->cnpj = $cnpj;

            return $empresa;
        }catch (\Exception $err) {
            return false;
        }
    }

    /**
     * Recupera o código IBGE de alguma cidade no país
     * @param $uf
     * @param $municipio
     * @return false|string|null false se deu erro, null se não achou, string com o código
     */
    private function getIBGECodeCity($uf, $municipio)
    {
        try{
            $response = Http::get('https://servicodados.ibge.gov.br/api/v1/localidades/estados/'.$uf.'/municipios');
            $responseData = $response->json();

            // procura a cidade no array de cidades
            foreach ($responseData as $cidade){
                if (Str::slug($cidade['nome']) === Str::slug($municipio)) {
                    // encontrou
                    return $cidade['id'];
                }
            }

            // não encontrou
            return null;
        }catch (\Exception $err) {
            return false;
        }
    }

}
