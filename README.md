## Arquitetura
<p style="text-align: center; width: 680px"><img src="/images/Planejamento.jpg"></p>

## Desafio
A API precisa ter no mínimo 3 endpointscom as seguintes funcionalidades:

    Upload de arquivo
    Histórico de upload de arquivo
    Buscar conteúdo do arquivo

As Regras de négocio:
Upload de arquivo:

    Deve ser possível enviar arquivos no formato Excel e CSV
    Não é permitido enviar o mesmo arquivo 2x

Histórico de upload de arquivo:

    Deve ser possível buscar um envio especifico por nome do arquivo ou data referência

Buscar conteúdo do arquivo:

    Neste endpoint é opcional o envio de parâmetros mas deve ser possível enviar no mínimo 2 informações para busca, que seriam os campos TckrSymb e RptDt.
    Se não enviar nenhum parâmetro o resultado deve ser apresentado páginado.
    O retorno esperado deve conter no mínimo essas informações:

## Tecnologia usadas
- PHP 8.2.24
- Laravel 11.27.2
- Laravel Sanctum
- Redis 3.2.5-alpine
- MongoDB mongodb-community-server:latest
- AWS S3

## Requisitos identificados
| # | Requisito      | Descrição                                                                                                                                                                             |
|---|----------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| 1 | Upload Arquivo | O arquivo deve ser do tipo csv, xls ou xlsx e só pode ser enviado uma única vez                                                                                                       |
| 2 | Histórico      | Deve existir um filtro por nome do arquivo ou data de referencia do arquivo e não pela data de upload.                                                                                |
| 3 | Buscar         | Deve existir um filtro com o envio de pelo menos 2 parâmetros, que serão os campos TckrSymb e RptDt, não obriogatório, caso os parâmetros estejam vazios o retorno deve ser paginado. |

## Utilização
Renomear o arquivo .env.example para .env e preencher as informações de acordo.
Informações que devem ser preenchidas:
- AWS_ACCESS_KEY_ID=
- AWS_SECRET_ACCESS_KEY=
- AWS_DEFAULT_REGION=us-east-1
- AWS_BUCKET=
- AWS_USE_PATH_STYLE_ENDPOINT=false

Crie um diretório na raiz do sistema operacional, /data, para servir como volume de dados do mongoDB.
Sete as permissoes para o diretório /data. Exemplo: chmod -R 777 /data. Para evitar problemas de permissões.

No arquivo docker-compose altere o volume da API.
Altere para onde estiver o projeto na sua máquina.
![docker-compose.yml](/images/docker-compose.png "Docker Compose")
Agora basta executar o comando:
```
docker compose up -d
```

## Documentação da API
Tanto a documentação quanto o export dos endpoints encontram-se no diretório doc.

