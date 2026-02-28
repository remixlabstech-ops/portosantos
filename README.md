# Porto Santos Advocacia — ERP Financeiro Jurídico

Sistema ERP financeiro completo para escritório jurídico, desenvolvido em **PHP 8+**, **MySQL** e **JavaScript moderno (ES6+)**.

---

## Como Baixar e Instalar

### ⬇️ Passo 1 — Baixar os arquivos

**Opção A — Download direto (sem Git, recomendado para iniciantes):**
1. Acesse: `https://github.com/remixlabstech-ops/portosantos`
2. Clique no botão verde **"Code"** → **"Download ZIP"**
3. Extraia o ZIP em alguma pasta do seu computador

**Opção B — Clonar com Git:**
```bash
git clone https://github.com/remixlabstech-ops/portosantos.git
cd portosantos
```

---

### 💻 Instalação Local (XAMPP / WAMP / Laragon)

> Escolha esta opção para rodar no seu próprio computador, sem precisar de hospedagem.

#### Pré-requisitos

| Software | Download |
|----------|----------|
| XAMPP (recomendado) | https://www.apachefriends.org |
| Ou WAMP (só Windows) | https://www.wampserver.com |
| Ou Laragon (só Windows) | https://laragon.org |

#### Passo a Passo

**1. Copiar os arquivos para o servidor local**

| Servidor | Pasta de destino |
|----------|-----------------|
| XAMPP (Windows) | `C:\xampp\htdocs\portosantos\` |
| XAMPP (Linux/Mac) | `/opt/lampp/htdocs/portosantos/` |
| WAMP | `C:\wamp64\www\portosantos\` |
| Laragon | `C:\laragon\www\portosantos\` |

Copie **todo o conteúdo** da pasta baixada para o caminho correspondente acima.

**2. Iniciar o XAMPP**

Abra o **XAMPP Control Panel** e clique em **Start** nos serviços:
- ✅ Apache
- ✅ MySQL

**3. Criar o banco de dados**

1. Abra o navegador e acesse: `http://localhost/phpmyadmin`
2. Clique em **"Novo"** (menu esquerdo)
3. Digite o nome `portosantos` e clique em **"Criar"**
4. Com o banco `portosantos` selecionado, clique na aba **"Importar"**
5. Clique em **"Escolher arquivo"** → selecione o arquivo `database.sql` da pasta do projeto
6. Clique em **"Executar"** (botão no final da página)

**4. Configurar o banco no projeto**

Abra o arquivo `config/database.php` em qualquer editor de texto (Notepad, VS Code, etc.) e confira as linhas:

```php
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');   // não alterar para XAMPP
define('DB_USER', getenv('DB_USER') ?: 'root');         // não alterar para XAMPP
define('DB_PASS', getenv('DB_PASS') ?: '');             // XAMPP usa senha em branco por padrão (altere se definiu uma senha)
define('DB_NAME', getenv('DB_NAME') ?: 'portosantos');  // nome do banco que você criou
define('DB_PORT', getenv('DB_PORT') ?: '3306');         // não alterar para XAMPP
```

> Para XAMPP com configuração padrão, os valores acima já estão corretos e **não precisam ser alterados**. Se você definiu uma senha para o MySQL durante a instalação, altere o valor de `DB_PASS`.

**5. Acessar o sistema**

Abra o navegador e acesse:
```
http://localhost/portosantos/
```

---

### 🌐 Instalação em Hospedagem (InfinityFree, Hostinger, etc.)

> Escolha esta opção para publicar o sistema na internet.

#### Pré-requisitos do servidor

| Requisito | Versão mínima |
|-----------|--------------|
| PHP | 8.0+ |
| MySQL | 5.7+ / MariaDB 10.3+ |
| Extensão PDO + pdo_mysql | habilitada |
| Apache | com mod_rewrite habilitado |

> **InfinityFree**: plano gratuito já inclui PHP 8 e MySQL ✅

#### Passo a Passo

**1. Enviar os arquivos via FTP**

1. Instale o [FileZilla](https://filezilla-project.org/) (gratuito)
2. No painel da hospedagem, localize as **credenciais FTP** (host, usuário, senha)
3. No FileZilla: **Arquivo → Gerenciador de Sites → Novo Site** → preencha os dados FTP
4. Conecte e envie **todos os arquivos** da pasta do projeto para `public_html/` (ou `htdocs/`)

**2. Criar e importar o banco de dados**

1. No painel da hospedagem, acesse o **phpMyAdmin**
2. Crie um banco de dados (anote o nome, usuário e senha gerados)
3. Selecione o banco, clique em **"Importar"** → escolha `database.sql` → **"Executar"**

**3. Configurar as credenciais**

Edite `config/database.php` com os dados fornecidos pela hospedagem:

```php
define('DB_HOST', getenv('DB_HOST') ?: 'host-do-banco-fornecido');
define('DB_USER', getenv('DB_USER') ?: 'usuario_do_banco');
define('DB_PASS', getenv('DB_PASS') ?: 'senha_do_banco');
define('DB_NAME', getenv('DB_NAME') ?: 'nome_do_banco');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
```

**4. Ajustar permissões das pastas de upload**

Via FileZilla ou terminal SSH:
```bash
chmod 775 uploads/
chmod 775 uploads/entradas/
chmod 775 uploads/saidas/
```

> **Nota:** `775` permite que o servidor web grave arquivos. Se o upload ainda falhar, tente `777` — mas use `777` apenas temporariamente e como último recurso, pois é menos seguro.

**5. Acessar o sistema**

```
https://seu-dominio.com/
```

---

## Estrutura de Arquivos

O repositório possui **56 arquivos** distribuídos da seguinte forma:

```
portosantos/                                  (56 arquivos)
│
├── .htaccess                                 # Regras Apache: mod_rewrite, bloqueio de listagem
├── README.md                                 # Esta documentação
├── database.sql                              # Schema + dados iniciais do banco
├── database_structure.sql                    # DDL puro (sem dados)
├── index.php                                 # Roteador principal (front controller)
│
├── config/
│   └── database.php                          # Conexão PDO (lê variáveis de ambiente)
│
├── api/                                      (9 arquivos)
│   ├── api_categorias.php
│   ├── api_centros_custo.php
│   ├── api_clientes.php
│   ├── api_dashboard.php
│   ├── api_entradas.php
│   ├── api_export.php                        # Exportação CSV de qualquer módulo
│   ├── api_fornecedores.php
│   ├── api_inadimplencia.php
│   └── api_saidas.php
│
├── controllers/                              (9 arquivos)
│   ├── BaseController.php
│   ├── CategoriaController.php
│   ├── CentroCustoController.php
│   ├── ClienteController.php
│   ├── DashboardController.php
│   ├── EntradaController.php
│   ├── FornecedorController.php
│   ├── InadimplenciaController.php
│   └── SaidaController.php
│
├── models/                                   (8 arquivos)
│   ├── BaseModel.php
│   ├── Categoria.php
│   ├── CentroCusto.php
│   ├── Cliente.php
│   ├── Dashboard.php
│   ├── Entrada.php
│   ├── Fornecedor.php
│   └── Saida.php
│
├── assets/
│   ├── css/
│   │   └── style.css                         # Estilo ERP (tema claro/escuro)
│   └── js/                                   (9 arquivos)
│       ├── app.js                            # Utilitários globais (modais, alertas, formatação)
│       ├── categorias.js
│       ├── centros_custo.js
│       ├── clientes.js
│       ├── dashboard.js                      # Gráficos Chart.js
│       ├── entradas.js
│       ├── fornecedores.js
│       ├── inadimplencia.js
│       └── saidas.js
│
├── views/                                    (12 arquivos)
│   ├── layout/
│   │   ├── header.php
│   │   ├── sidebar.php
│   │   └── footer.php
│   ├── categorias/index.php
│   ├── centros_custo/index.php
│   ├── clientes/index.php
│   ├── dashboard/index.php
│   ├── entradas/index.php
│   ├── fornecedores/index.php
│   ├── inadimplencia/index.php
│   └── saidas/index.php
│
└── uploads/                                  (3 arquivos .gitkeep)
    ├── .gitkeep
    ├── entradas/.gitkeep                     # Comprovantes de entradas (PDF)
    └── saidas/.gitkeep                       # Comprovantes de saídas (PDF)
```

> **Como verificar:** após baixar/clonar o repositório, execute o comando abaixo e confira se o total é **56**:
> ```bash
> find . -not -path './.git/*' -type f | wc -l
> ```

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