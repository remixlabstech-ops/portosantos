/* Fornecedores JS */
let fornecedoresData = [];

document.addEventListener('DOMContentLoaded', () => {
    carregarFornecedores();
    initModalFornecedor();
});

function carregarFornecedores() {
    mostrarCarregando(document.getElementById('tabela-fornecedores'));
    fetch(`${API_BASE}api_fornecedores.php?action=listar`)
        .then(r => r.json())
        .then(data => { fornecedoresData = data.data || []; renderTabelaFornecedores(fornecedoresData); })
        .catch(() => mostrarAlerta('Erro ao carregar fornecedores', 'erro'));
}

function renderTabelaFornecedores(lista) {
    const container = document.getElementById('tabela-fornecedores');
    if (!container) return;

    if (!lista.length) {
        container.innerHTML = '<p style="text-align:center;padding:30px;color:var(--text-muted)"><i class="fas fa-truck"></i> Nenhum fornecedor cadastrado.</p>';
        return;
    }

    const rows = lista.map(f => `
        <tr>
            <td><strong>${f.nome}</strong></td>
            <td>${f.cnpj || '—'}</td>
            <td>${f.email || '—'}</td>
            <td>${f.telefone || '—'}</td>
            <td>${f.endereco || '—'}</td>
            <td>
                <button class="btn-acao-edit" onclick="editarFornecedor(${f.id})"><i class="fas fa-edit"></i></button>
                <button class="btn-acao-delete" onclick="deletarFornecedor(${f.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('');

    container.innerHTML = `<table>
        <thead><tr>
            <th>Nome</th><th>CNPJ</th><th>E-mail</th><th>Telefone</th><th>Endereço</th><th>Ações</th>
        </tr></thead>
        <tbody>${rows}</tbody>
    </table>`;
}

function initModalFornecedor() {
    document.getElementById('btn-novo-fornecedor')?.addEventListener('click', () => abrirModalFornecedor());
    document.getElementById('fechar-modal-fornecedor')?.addEventListener('click', fecharModalFornecedor);
    document.getElementById('cancelar-modal-fornecedor')?.addEventListener('click', fecharModalFornecedor);
    document.getElementById('salvar-fornecedor')?.addEventListener('click', salvarFornecedor);
}

function abrirModalFornecedor(f = null) {
    limparFormFornecedor();
    const title = document.getElementById('modal-fornecedor-title');
    if (f) {
        title.innerHTML = '<i class="fas fa-edit"></i> Editar Fornecedor';
        document.getElementById('fornecedor-id').value       = f.id;
        document.getElementById('fornecedor-nome').value     = f.nome;
        document.getElementById('fornecedor-cnpj').value     = f.cnpj     || '';
        document.getElementById('fornecedor-email').value    = f.email    || '';
        document.getElementById('fornecedor-telefone').value = f.telefone || '';
        document.getElementById('fornecedor-endereco').value = f.endereco || '';
    } else {
        title.innerHTML = '<i class="fas fa-truck"></i> Novo Fornecedor';
    }
    document.getElementById('modal-fornecedor').style.display = 'flex';
}

function fecharModalFornecedor() {
    document.getElementById('modal-fornecedor').style.display = 'none';
}

function limparFormFornecedor() {
    ['fornecedor-id','fornecedor-nome','fornecedor-cnpj','fornecedor-email','fornecedor-telefone','fornecedor-endereco']
        .forEach(id => { const el = document.getElementById(id); if (el) el.value = id === 'fornecedor-id' ? '0' : ''; });
}

async function salvarFornecedor() {
    const id = parseInt(document.getElementById('fornecedor-id').value) || 0;
    const payload = {
        nome:     document.getElementById('fornecedor-nome').value.trim(),
        cnpj:     document.getElementById('fornecedor-cnpj').value.trim(),
        email:    document.getElementById('fornecedor-email').value.trim(),
        telefone: document.getElementById('fornecedor-telefone').value.trim(),
        endereco: document.getElementById('fornecedor-endereco').value.trim(),
    };
    if (!payload.nome) { mostrarAlerta('Informe o nome do fornecedor.', 'aviso'); return; }

    try {
        const url  = id ? `${API_BASE}api_fornecedores.php?action=atualizar&id=${id}` : `${API_BASE}api_fornecedores.php?action=criar`;
        const r    = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
        const data = await r.json();
        if (data.success) { mostrarAlerta(data.message, 'sucesso'); fecharModalFornecedor(); carregarFornecedores(); }
        else mostrarAlerta(data.message, 'erro');
    } catch { mostrarAlerta('Falha de conexão.', 'erro'); }
}

function editarFornecedor(id) {
    const f = fornecedoresData.find(x => x.id == id);
    if (f) abrirModalFornecedor(f);
}

async function deletarFornecedor(id) {
    const ok = await confirmarAcao('Deseja realmente excluir este fornecedor?');
    if (!ok) return;
    const r    = await fetch(`${API_BASE}api_fornecedores.php?action=deletar&id=${id}`);
    const data = await r.json();
    if (data.success) { mostrarAlerta(data.message, 'sucesso'); carregarFornecedores(); }
    else mostrarAlerta(data.message, 'erro');
}
