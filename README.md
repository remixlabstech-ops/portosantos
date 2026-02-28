# Porto Santos ERP — Sistema Financeiro Jurídico

Sistema ERP financeiro completo para escritório jurídico, desenvolvido em PHP 8+, MySQL e JavaScript moderno.

## Stack Tecnológico

- **Backend:** PHP 8+ puro (sem framework), arquitetura MVC
- **Banco de dados:** MySQL / MariaDB com PDO + Prepared Statements
- **Frontend:** JavaScript ES6+, Chart.js, CSS moderno com variáveis
- **Ícones:** Lucide Icons
- **Hospedagem:** Compatível com InfinityFree e outros hosts compartilhados

## Estrutura do Projeto

```
portosantos/
├── config/
│   ├── database.php       # Credenciais do banco (PDO)
│   └── constants.php      # Constantes da aplicação
├── controllers/
│   ├── BaseController.php
│   ├── DashboardController.php
│   ├── EntradasController.php
│   ├── SaidasController.php
│   ├── InadimplenciaController.php
│   └── CadastrosController.php
├── models/
│   ├── Database.php       # Singleton PDO
│   ├── BaseModel.php      # CRUD genérico
│   ├── Cliente.php
│   ├── Fornecedor.php
│   ├── Entrada.php
│   ├── Saida.php
│   ├── Parcela.php
│   ├── Rateio.php
│   ├── CentroCusto.php
│   ├── Categoria.php
│   └── Log.php
├── views/
│   ├── layout/
│   │   ├── header.php
│   │   ├── sidebar.php
│   │   └── footer.php
│   ├── dashboard.php
│   ├── entradas.php
│   ├── saidas.php
│   ├── inadimplencia.php
│   ├── cadastros.php
│   ├── clientes.php
│   ├── fornecedores.php
│   ├── centros_custo.php
│   └── error.php
├── api/
│   ├── Response.php
│   ├── api_clientes.php
│   ├── api_fornecedores.php
│   ├── api_entradas.php
│   ├── api_saidas.php
│   ├── api_rateios.php
│   ├── api_parcelas.php
│   ├── api_inadimplencia.php
│   ├── api_centros_custo.php
│   └── api_categorias.php
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   ├── components.css
│   │   └── responsive.css
│   └── js/
│       ├── app.js
│       ├── utils.js
│       ├── modals.js
│       ├── entradas.js
│       ├── saidas.js
│       └── inadimplencia.js
├── uploads/
├── dashboard.php
├── entradas.php
├── saidas.php
├── inadimplencia.php
├── cadastros.php
├── index.php
└── database_structure.sql
```

## Instalação

### 1. Configurar Banco de Dados

Execute o script SQL para criar as tabelas:

```bash
mysql -h sql100.infinityfree.com -u if0_41229268 -p if0_41229268_ps < database_structure.sql
```

### 2. Configurar Credenciais

Edite `config/database.php` com as credenciais do seu banco:

```php
define('DB_HOST', 'seu_host');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'seu_banco');
```

Ou use variáveis de ambiente:

```bash
export DB_HOST=sql100.infinityfree.com
export DB_USER=if0_41229268
export DB_PASS=sua_senha_segura
export DB_NAME=if0_41229268_ps
```

### 3. Permissões de Upload

```bash
chmod 755 uploads/
```

### 4. Acesso

Abra o navegador em `http://seu-dominio/` ou `http://seu-dominio/dashboard.php`.

## Funcionalidades

### Dashboard
- KPI cards: Entradas, Saídas, Lucro Líquido, A Receber, A Pagar
- Variação percentual vs mês anterior
- Gráfico de barras: Entradas vs Saídas (12 meses)
- Ranking Top 5 Clientes mais rentáveis
- Ranking Top 5 Centros de Custo

### Entradas (Honorários)
- Autocomplete AJAX de clientes
- Categorias jurídicas: Cível, Trabalhista, Previdenciário, Criminal
- Tipos de honorário: Contratual, Avulso, Sucumbência, Êxito
- Cálculo automático para Sucumbência/Êxito (valor = causa × %)
- Parcelamento em 1–12 parcelas com preview
- Upload de comprovante (PDF, máx 5 MB)
- Soft delete + log de auditoria

### Saídas (Despesas)
- Autocomplete AJAX de fornecedores
- Taxa percentual ou valor fixo
- Centro de custo
- Rateio avançado (cliente, múltiplos clientes, administrativo)
- Validação: soma de rateios deve ser 100%
- Parcelamento 1–12 parcelas

### Inadimplência
- Filtros por faixa: 5, 10, 15, 20, 30, 45, 60, 90, 120, 180 dias
- Indicadores: Total em atraso, Média de dias, Maior atraso
- Tabela de faixas de inadimplência
- Ações: Marcar como Pago, Renegociar (nova data)

### Cadastros (CRUD)
- Clientes (com paginação e edição inline via modal)
- Fornecedores
- Centros de Custo
- Categorias

## Segurança

- Todas as queries usam PDO + Prepared Statements
- Inputs sanitizados com `htmlspecialchars`
- Validação frontend + backend dupla
- Upload com validação de MIME type (somente PDF)
- Soft delete (dados nunca apagados fisicamente)
- Log completo de todas as operações (INSERT, UPDATE, DELETE)
- Estrutura preparada para multiusuário e SaaS (campo `empresa`)

## Configuração InfinityFree

1. Crie o banco de dados no painel do InfinityFree
2. Execute o `database_structure.sql` via phpMyAdmin
3. Faça upload de todos os arquivos via FileZilla/cPanel
4. Edite `config/database.php` com os dados do banco
5. Garanta que a pasta `uploads/` tem permissão 755

## Licença

Uso interno — Porto Santos Advocacia.
