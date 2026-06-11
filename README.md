# Nave Supernova ISS

## Visão geral

Pacote privado para integração do Laravel com o Supernova ISS. Ele é consumido via Composer com repositório VCS e registra automaticamente a configuração do pacote e as rotas internas.

## Requisitos

- PHP 8.2 ou superior
- Laravel 8, 9, 10, 11 ou 12
- Composer 2
- Acesso ao repositório privado no GitHub

## Acesso a repositórios privados

No projeto cliente, declare o repositório VCS antes de instalar o pacote:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/appnave/nave-supernova-iss"
    }
  ]
}
```

Depois, instale o pacote:

```bash
composer require appnave/nave-supernova-iss
```

Se o projeto cliente também consumir outros repositórios privados, mantenha a mesma estratégia de autenticação no Composer.

### Autenticação local

```bash
composer config -g github-oauth.github.com <YOUR_TOKEN>
```

### GitHub Actions

```yaml
env:
  COMPOSER_AUTH: >-
    {"github-oauth":{"github.com":"${{ secrets.COMPOSER_GITHUB_TOKEN }}"}}
```

## Instalação local

1. Adicione o repositório VCS no `composer.json` do projeto cliente.
2. Instale o pacote com `composer require appnave/nave-supernova-iss`.
3. Publique a configuração do pacote.

```bash
php artisan vendor:publish --tag=iss-supernova-config
```

4. Configure as variáveis de ambiente no projeto cliente.

```env
MS_SUPERNOVA_BASE_URI=https://sua-url-do-supernova
MS_SUPERNOVA_API_PREFIX=/api
MS_SUPERNOVA_DB_HOST=127.0.0.1
MS_SUPERNOVA_DB_PORT=3306
MS_SUPERNOVA_DB_DATABASE=iss_supernova
MS_SUPERNOVA_DB_USERNAME=root
MS_SUPERNOVA_DB_PASSWORD=secret
MS_SUPERNOVA_COMPANIES=uuid-1,uuid-2,uuid-3
```

5. Garanta que o projeto cliente tenha a configuração `hub` necessária para obter o token de acesso:

- `hub.base_uri`
- `hub.oauth.token_uri`
- `hub.programatic_access.client_id`
- `hub.programatic_access.client_secret`

## Comandos úteis

```bash
composer test
composer analyse
composer check-style
composer fix-style
```

## Informações adicionais

- O pacote registra a conexão de banco `iss-supernova` com base nas variáveis `MS_SUPERNOVA_DB_*`.
- O namespace PHP principal é `Bildvitta\IssSupernova`.
- A facade `IssSupernova` é registrada automaticamente pelo pacote.
- A rota utilitária `GET /api/supernova/trigger-event` é carregada pelo pacote e pode ser usada em ambientes de apoio/local para disparar eventos informados via query string.
