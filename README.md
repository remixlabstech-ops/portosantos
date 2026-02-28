# Porto Santos Advocacia — ERP Financeiro Jurídico

Sistema ERP financeiro completo para escritório jurídico, desenvolvido em **PHP 8+**, **MySQL** e **JavaScript moderno (ES6+)**.

---

## Como Baixar os Arquivos / How to Download

### Opção 1 — Baixar como ZIP (sem Git)

1. Acesse a página do repositório no GitHub:  
   `https://github.com/remixlabstech-ops/portosantos`
2. Clique no botão verde **"Code"** (canto superior direito).
3. Selecione **"Download ZIP"**.
4. Extraia o arquivo ZIP no diretório raiz do seu servidor web (ex.: `public_html` no InfinityFree).

### Opção 2 — Clonar com Git

```bash
git clone https://github.com/remixlabstech-ops/portosantos.git
cd portosantos
```

---

## Pré-requisitos

| Requisito | Versão mínima |
|-----------|--------------|
| PHP       | 8.0+         |
| MySQL     | 5.7+ / MariaDB 10.3+ |
| Extensão PDO + pdo_mysql | habilitada |
| Servidor web | Apache (com mod_rewrite) ou Nginx |

> **InfinityFree**: o plano gratuito já inclui PHP 8 e MySQL. Basta usar o painel de controle deles.

---

## Instalação Passo a Passo

### 1. Enviar os arquivos para o servidor

**InfinityFree (via FTP):**
1. No painel InfinityFree, vá em **"File Manager"** ou use um cliente FTP (ex.: FileZilla).
2. Conecte-se com as credenciais FTP exibidas no painel.
3. Envie **todos os arquivos** da pasta `portosantos/` para `htdocs/` (ou `public_html/`).

**Servidor local (XAMPP / WAMP / Laragon):**
```bash
# Copie a pasta para o diretório web do XAMPP
cp -r portosantos/ /xampp/htdocs/portosantos/
```

### 2. Criar e importar o banco de dados

**Via phpMyAdmin (InfinityFree, XAMPP, etc.):**
1. Acesse o **phpMyAdmin** pelo painel do seu host.
2. Crie um banco de dados com o nome desejado (ex.: `if0_41229268_ps`).
3. Selecione o banco criado e clique na aba **"Importar"**.
4. Escolha o arquivo `database.sql` (raiz do projeto) e clique em **"Executar"**.

**Via linha de comando:**
```bash
mysql -u SEU_USUARIO -p NOME_DO_BANCO < database.sql
```

### 3. Configurar as credenciais do banco

Edite o arquivo `config/database.php` com os dados do seu banco:

```php
define('DB_HOST', 'seu-host-do-banco');  // ex.: sql100.infinityfree.com
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'nome_do_banco');
define('DB_PORT', '3306');
```

> **Recomendado em produção:** defina variáveis de ambiente no servidor em vez de editar o arquivo diretamente.

### 4. Criar o usuário administrador

Gere um hash seguro para a senha do admin (execute uma única vez):

```bash
php -r "echo password_hash('SUA_SENHA_FORTE', PASSWORD_BCRYPT, ['cost' => 12]);"
```

Em seguida, execute no phpMyAdmin ou via CLI:

```sql
INSERT INTO `usuarios` (`nome`, `email`, `senha`, `perfil`, `ativo`)
VALUES ('Administrador', 'admin@portosantos.adv.br', '<hash_gerado_acima>', 'admin', 1);
```

### 5. Permissões de diretório

Garanta que o servidor web possa gravar nos diretórios de upload:

```bash
chmod 755 uploads/
chmod 755 uploads/entradas/
chmod 755 uploads/saidas/
```

### 6. Acessar o sistema

Abra o navegador e acesse:

```
http://localhost/portosantos/          (servidor local)
https://seu-dominio.infinityfree.net/  (InfinityFree)
```

---

## Estrutura de Arquivos

```
portosantos/
├── api/                  # Endpoints JSON (CRUD + exportação)
├── assets/
│   ├── css/style.css     # Estilo ERP (tema claro/escuro)
│   └── js/               # Módulos JavaScript (ES6+)
├── config/
│   └── database.php      # Conexão PDO
├── controllers/          # Lógica de negócio
├── models/               # Acesso ao banco de dados
├── uploads/
│   ├── entradas/         # Comprovantes de entradas (PDF)
│   └── saidas/           # Comprovantes de saídas (PDF)
├── views/
│   ├── layout/           # Header, sidebar e footer
│   ├── dashboard/        # Painel principal com gráficos
│   ├── entradas/         # Módulo de honorários
│   ├── saidas/           # Módulo de despesas
│   ├── clientes/         # Cadastro de clientes
│   ├── fornecedores/     # Cadastro de fornecedores
│   ├── centros_custo/    # Centros de custo
│   ├── categorias/       # Categorias receita/despesa
│   └── inadimplencia/    # Controle de inadimplência
├── database.sql          # Schema completo do banco
└── index.php             # Roteador principal
```

---

## Funcionalidades

- **Dashboard** com gráficos (Chart.js): comparativo mensal, distribuição por área jurídica
- **Entradas** (honorários): Cível, Trabalhista, Previdenciário, Criminal — cálculo automático para Sucumbência/Êxito
- **Saídas** com rateio por cliente único, múltiplos clientes (validação 100%) ou administrativo
- **Inadimplência** com filtros por faixas de dias e ranking de maiores devedores
- **Cadastros completos**: Clientes, Fornecedores, Centros de Custo, Categorias
- **Exportação CSV** de qualquer módulo
- **Upload de comprovantes** (somente PDF)
- **Tema claro/escuro** alternável
- **Parcelamento automático** de entradas e saídas

---

## Segurança

- Todas as queries usam **PDO + Prepared Statements** (proteção contra SQL Injection)
- Uploads validados por tipo MIME no servidor (somente PDF)
- Exportação CSV com proteção contra **formula injection**
- `.htaccess` bloqueia listagem de diretórios e acesso direto a arquivos `.sql` e `.env`