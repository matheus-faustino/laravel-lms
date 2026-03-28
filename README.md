## Requisitos

- [Docker](https://www.docker.com/get-started) e [Docker Compose](https://docs.docker.com/compose/install/)
- [PHP 8.3+](https://www.php.net/) e [Composer](https://getcomposer.org/) *(apenas para a instalação inicial)*

---

## Setup com Laravel Sail

### 1. Clone o repositório

```bash
git clone <url-do-repositorio>
cd <nome-do-projeto>
```

### 2. Instale as dependências PHP

Na primeira vez, instale as dependências via Composer sem precisar de PHP local (usando a imagem Docker do Sail):

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

Ou, se tiver o PHP 8.3+ instalado localmente:

```bash
composer install
```

### 3. Configure o arquivo de ambiente

```bash
cp .env.example .env
```

### 4. Suba os containers

```bash
./vendor/bin/sail up -d
```

> **Dica:** Para criar um alias e usar `sail` diretamente no terminal, adicione ao seu `~/.bashrc` ou `~/.zshrc`:
> ```bash
> alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
> ```

### 5. Gere a chave da aplicação

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Execute as migrations

```bash
./vendor/bin/sail artisan migrate
```

### 7. Instale as dependências front-end e compile os assets

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

A aplicação estará disponível em [http://localhost](http://localhost).

---

## Uso diário

| Comando | Descrição |
|---|---|
| `sail up -d` | Inicia os containers em background |
| `sail down` | Para e remove os containers |
| `sail artisan <comando>` | Executa um comando Artisan |
| `sail composer <comando>` | Executa um comando Composer |
| `sail npm <comando>` | Executa um comando npm |
| `sail npm run dev` | Inicia o Vite em modo de desenvolvimento |
| `sail test` | Executa a suíte de testes |
| `sail shell` | Abre um shell dentro do container |
| `sail tinker` | Abre o REPL interativo do Laravel |

---

## Testes

```bash
./vendor/bin/sail test
```

---

## Serviços disponíveis

Por padrão, o Sail expõe os seguintes serviços:

| Serviço | URL/Porta |
|---|---|
| Aplicação | http://localhost |
| MySQL | `localhost:3306` |
| Redis | `localhost:6379` |
| Mailpit | http://localhost:8025 |
| PHPMyAdmin | http://localhost:8080 |

Para adicionar ou remover serviços, publique o `docker-compose.yml` e edite conforme necessário:

```bash
./vendor/bin/sail artisan sail:publish
```

---

## Licença

Este projeto é um software open-source licenciado sob a [licença MIT](https://opensource.org/licenses/MIT).
