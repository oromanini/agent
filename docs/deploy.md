# Deploy — agent (Hostinger, hospedagem compartilhada)

Deploy automático: **push na branch `master`** → GitHub Actions conecta via SSH e atualiza o servidor.
Workflow: [`.github/workflows/deploy.yml`](../.github/workflows/deploy.yml).
Também dá pra rodar manualmente em **Actions → Deploy (Hostinger) → Run workflow**.

## O que o pipeline faz (deploy-only, sem testes)

1. `git reset --hard origin/master` no diretório do app no servidor
2. `composer install --no-dev --optimize-autoloader --ignore-platform-req=php`
3. `artisan config:cache` → `route:cache` → `view:cache` → `storage:link` → `queue:restart`

Assets (`public/css`, `public/js`) são versionados no repo, então **não há build de front no deploy**.

> **Migrations não rodam no deploy.** A tabela `migrations` do servidor está fora de
> sincronia com o schema (herança da divergência `main`/`master`), então `artisan migrate`
> quebra em colunas que já existem. Aplicar migrations manualmente, reconciliando antes.

## Setup único

### 1. Chave SSH de deploy

Já gerada nesta sessão (par ed25519 dedicado, sem passphrase).

**No servidor** (logado como `u441227450`):

```bash
mkdir -p ~/.ssh && chmod 700 ~/.ssh
echo "ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIJzUlo+ezya/k9PEijjmINEeOHjsY683dCjpcbh2PBt0 github-actions-deploy-agent" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

A **chave privada** correspondente foi exibida no chat — guarde no secret `SSH_PRIVATE_KEY` (abaixo) e não a comite.

### 2. Secrets do repositório (Settings → Secrets and variables → Actions → Secrets)

| Secret | Valor |
|---|---|
| `SSH_HOST` | `185.245.180.207` |
| `SSH_USER` | `u441227450` |
| `SSH_PORT` | `65002` |
| `SSH_PRIVATE_KEY` | conteúdo da chave privada ed25519 (bloco `-----BEGIN OPENSSH PRIVATE KEY-----` … `-----END …-----`) |
| `DEPLOY_PATH` | `/home/u441227450/domains/alluzenergia.com.br/public_html/agent` |

### 3. Variables opcionais (mesma tela → Variables)

Não são necessárias: o servidor já tem `php` (8.1.34) e `composer` (2.9.8) no PATH.
Definir `PHP_BIN` / `COMPOSER_BIN` só se isso mudar.

### 4. Diretório no servidor

Já está pronto: `DEPLOY_PATH` já é um clone git na branch `master`, com `.env` de produção
no lugar e escrita OK. O deploy só faz `git fetch` + `git reset --hard origin/master`.

## Pendências / observações

- **PHP 8.1 no servidor × `composer.json` pede `^8.2`.** O workflow usa
  `composer install --ignore-platform-req=php` como contorno. Ideal: subir o PHP do
  servidor para 8.2+ ou reverter o bump de versão na `master`.
- `master` e `main` divergiram: `master` tem o redesign (Claude Design / tema claro,
  PRs #1–#33); `main` tem a compressão de PDF via Ghostscript (PR #34,
  `app/Services/PdfCompressionService.php`) que **não está na `master`**. O `gs` já está
  instalado no servidor (`/usr/bin/gs`), então dá pra portar a feature quando quiser.
- Deploy roda `artisan migrate --force` — cuidado com migrations destrutivas na `master`.
