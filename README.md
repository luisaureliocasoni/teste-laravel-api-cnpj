# API FastPonto Teste

API para demonstração das habilidades de Laravel

## Requisitos
- PHP 8.1 instalado
- MySQL instalado
OU
- Docker instalado

## Rodando a aplicação usando Docker
1. Copie o .env.example para .env
2. No .env configure a porta pública da API (APP_PORT) e porta pública do Banco de Dados (FORWARD_DB_PORT)
3. `./vendor/bin/sail up` - para iniciar os containers
4. `./vendor/bin/sail artisan migrate` - para rodar as migrações

## Testando usando docker
1. `./vendor/bin/sail artisan make:test <nometeste>` - para criar o teste
2. `./vendor/bin/sail test` - para rodar os testes - por causa do fato de que a aplicação está usando a API pública do ReceitaWS, recomenda-se rodar os testes apenas 1 vez por minuto.
