# Sistema de Cadastro e Gerenciamento de Livros

Aplicação web desenvolvida em PHP com Symfony 7.4, utilizando arquitetura MVC com camada de Regras de Negócio (RN), Doctrine ORM e MySQL.

O sistema permite cadastrar e gerenciar livros, autores e assuntos, além de disponibilizar um relatório de livros agrupados por autor, com filtros independentes de situação.

## 1. Tecnologias utilizadas

- PHP 8.4
- Symfony 7.4
- Doctrine ORM 3.7
- Doctrine Migrations 4.0
- Doctrine Fixtures 4.3
- MySQL 8.4
- Twig
- HTML5, CSS3 e JavaScript
- jQuery
- Bootstrap 5.3.3
- Docker e Docker Compose
- PHPUnit

## 2. Arquitetura do projeto

O projeto utiliza o padrão MVC (Model-View-Controller), com uma camada adicional de Regras de Negócio (RN), responsável por concentrar as validações e operações específicas do sistema.

### Fluxo da aplicação

Requisição HTTP → Router → Controller → RN → Repository / EntityManager → Doctrine ORM → MySQL.

### Responsabilidade das camadas

**Controller**
- Recebe e interpreta as requisições HTTP.
- Encaminha as operações para a camada RN.
- Retorna as respostas e renderiza os templates Twig.

**RN (Regras de Negócio)**
- Centraliza as validações.
- Executa as regras de negócio.
- Coordena as operações entre entidades e repositórios.
- Controla as operações de cadastro, edição e alteração de situação.

**Repository**
- Centraliza as consultas ao banco de dados.
- Disponibiliza métodos de busca e recuperação de registros.

**Entity**
- Representa as entidades do domínio.
- Define os atributos e relacionamentos.
- É utilizada pelo Doctrine ORM para persistência dos dados.

**Twig**
- Responsável pela apresentação das páginas.
- Utiliza componentes reutilizáveis para elementos comuns da interface.

## 3. Estrutura do banco de dados

O banco de dados utiliza cinco tabelas principais:

| Tabela | Descrição |
|---|---|
| Livro | Armazena os dados dos livros |
| Autor | Armazena os autores |
| Assunto | Armazena os assuntos |
| Livro_Autor | Relacionamento entre livros e autores |
| Livro_Assunto | Relacionamento entre livros e assuntos |

### Relacionamentos

- Livro possui relacionamento ManyToMany com Autor.
- Livro possui relacionamento ManyToMany com Assunto.
- Um livro pode possuir vários autores.
- Um autor pode estar associado a vários livros.
- Um livro pode possuir vários assuntos.
- Um assunto pode classificar vários livros.

O relacionamento é persistido por meio das tabelas intermediárias `Livro_Autor` e `Livro_Assunto`.

### Exclusão lógica

As entidades Livro, Autor e Assunto possuem o atributo `Ativo`.

A exclusão é realizada logicamente, alterando a situação do registro, sem remover fisicamente os dados do banco.

Regras implementadas:
- Registros inativos não aparecem nas listagens padrão.
- Registros inativos não podem ser utilizados em novos relacionamentos.
- Relacionamentos existentes são preservados quando uma entidade é inativada.
- Registros podem ser reativados.

## 4. Funcionalidades

### Livros
- Cadastro de livros.
- Edição de informações.
- Associação de autores.
- Associação de assuntos.
- Listagem de livros ativos e inativos.
- Inativação e reativação.

### Autores
- Cadastro de autores.
- Edição de informações.
- Listagem de autores ativos e inativos.
- Inativação e reativação.

### Assuntos
- Cadastro de assuntos.
- Edição de informações.
- Listagem de assuntos ativos e inativos.
- Inativação e reativação.

### Relatórios
- Relatório de livros agrupados por autor.
- Exibição dos dados dos livros, autores e assuntos.
- Filtro independente de situação para Livro, Autor e Assunto.
- Filtro por autor específico.
- Filtro por assunto específico.
- Visualização dos registros ativos e inativos.

O relatório utiliza a VIEW `vw_relatorio_livros_autores`, criada por migration no banco de dados.

A VIEW considera os relacionamentos entre livros, autores e assuntos, permitindo que livros associados a vários autores sejam apresentados nos respectivos agrupamentos.

## 5. Requisitos para execução

Para executar o projeto, é necessário possuir:

- Docker Desktop ou Docker Engine.
- Docker Compose.
- Git.

Não é necessário instalar PHP, Composer ou MySQL diretamente na máquina, pois esses serviços são executados nos containers Docker.

## 6. Instalação e execução

### 6.1. Clonar o repositório

```bash
git clone URL_DO_REPOSITORIO
cd sistema-livros
```

### 6.2. Iniciar os containers

```bash
docker compose up -d --build
```

O Docker irá construir a imagem da aplicação e iniciar os serviços necessários:

- Aplicação PHP com Apache.
- Banco de dados MySQL.
- phpMyAdmin.
- Mailpit.

### 6.3. Instalar as dependências

```bash
docker compose exec app composer install
```

O Composer instalará as dependências definidas no `composer.lock`, incluindo as dependências de desenvolvimento necessárias para executar as Fixtures.

### 6.4. Criar a estrutura do banco

Execute as migrations:

```bash
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
```

As migrations criarão as tabelas, os relacionamentos, os campos de situação e a VIEW utilizada pelo relatório.

### 6.5. Carregar os dados demonstrativos

Execute:

```bash
docker compose exec app php bin/console doctrine:fixtures:load --no-interaction
```

A carga demonstrativa inclui livros, autores e assuntos, contemplando diferentes situações e relacionamentos.

**Atenção:** o comando padrão de Fixtures limpa os registros das entidades mapeadas antes de inserir os dados demonstrativos. Execute-o somente em um banco preparado para a avaliação. Não o execute em um banco que contenha informações que deseja preservar.

## 7. Endereços de acesso

Após a inicialização dos containers, os serviços estarão disponíveis nos seguintes endereços:

| Serviço | Endereço |
|---|---|
| Sistema de Livros | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 |
| Mailpit | http://localhost:8025 |

### Acesso ao banco de dados

Configurações padrão do ambiente Docker:

| Parâmetro | Valor |
|---|---|
| Host interno | db |
| Porta | 3306 |
| Banco | sistema_livros |
| Usuário | app |
| Senha | app_secret |
| Usuário administrador | root |
| Senha administrador | root_secret |

As credenciais são destinadas exclusivamente ao ambiente local de desenvolvimento e avaliação.

## 8. Estrutura de diretórios

```text
sistema-livros/
├── config/
├── docker/
│   ├── apache/
│   └── php/
├── public/
│   ├── css/
│   └── js/
├── src/
│   ├── Controller/
│   ├── DataFixtures/
│   ├── Entity/
│   ├── Form/
│   ├── Migrations/
│   ├── Repository/
│   └── RN/
├── templates/
│   ├── components/
│   ├── home/
│   ├── livro/
│   ├── autor/
│   ├── assunto/
│   └── relatorio/
├── tests/
├── compose.yaml
├── compose.override.yaml
├── composer.json
├── composer.lock
└── README.md
```

## 9. Comandos úteis

Consultar o status das migrations:

```bash
docker compose exec app php bin/console doctrine:migrations:status
```

Validar o mapeamento e a estrutura do banco:

```bash
docker compose exec app php bin/console doctrine:schema:validate
```

Consultar as rotas:

```bash
docker compose exec app php bin/console debug:router
```

Consultar os comandos disponíveis:

```bash
docker compose exec app php bin/console list
```

Parar os containers:

```bash
docker compose down
```

Para remover também o volume do banco e reconstruir o ambiente do zero:

```bash
docker compose down -v
```

**Atenção:** o último comando apaga os dados armazenados no volume MySQL. Utilize-o somente quando desejar reiniciar completamente o ambiente.

---

## 10. Considerações finais

O projeto foi desenvolvido com separação de responsabilidades entre apresentação, controle, regras de negócio e persistência, buscando facilitar a manutenção e a evolução do sistema.

A utilização de Docker permite reproduzir o ambiente de execução em diferentes máquinas, enquanto as migrations e Fixtures permitem reconstruir a estrutura do banco e carregar dados demonstrativos para avaliação.