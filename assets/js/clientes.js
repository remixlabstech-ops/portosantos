/* Clientes JS */
let clientesData = [];

document.addEventListener('DOMContentLoaded', () => {
    carregarClientes();
    initModalCliente();
});

function carregarClientes() {
    mostrarCarregando(document.getElementById('tabela-clientes'));
    fetch(`${API_BASE}api_clientes.php?action=listar`)
        .then(r => r.json())
        .then(data => { clientesData = data.data || []; renderTabelaClientes(clientesData); })
        .catch(() => mostrarAlerta('Erro ao carregar clientes', 'erro'));
}

function renderTabelaClientes(lista) {
    const container = document.getElementById('tabela-clientes');
    if (!container) return;

    if (!lista.length) {
        container.innerHTML = '<p style="text-align:center;padding:30px;color:var(--text-muted)"><i class="fas fa-users"></i> Nenhum cliente cadastrado.</p>';
        return;
    }

    const rows = lista.map(c => `
        <tr>
            <td><strong>${c.nome}</strong></td>
            <td>${c.cpf || '—'}</td>
            <td>${c.email || '—'}</td>
            <td>${c.telefone || '—'}</td>
            <td>${c.endereco || '—'}</td>
            <td>
                <button class="btn-acao-edit" onclick="editarCliente(${c.id})"><i class="fas fa-edit"></i></button>
                <button class="btn-acao-delete" onclick="deletarCliente(${c.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('');

    container.innerHTML = `<table>
        <thead><tr>
            <th>Nome</th><th>CPF</th><th>E-mail</th><th>Telefone</th><th>Endereço</th><th>Ações</th>
        </tr></thead>
        <tbody>${rows}</tbody>
    </table>`;
}

function initModalCliente() {
    document.getElementById('btn-novo-cliente')?.addEventListener('click', () => abrirModalCliente());
    document.getElementById('fechar-modal-cliente')?.addEventListener('click', fecharModalCliente);
    document.getElementById('cancelar-modal-cliente')?.addEventListener('click', fecharModalCliente);
    document.getElementById('salvar-cliente')?.addEventListener('click', salvarCliente);
}

function abrirModalCliente(c = null) {
    limparFormCliente();
    const title = document.getElementById('modal-cliente-title');
    if (c) {
        title.innerHTML = '<i class="fas fa-edit"></i> Editar Cliente';
        document.getElementById('cliente-id').value       = c.id;
        document.getElementById('cliente-nome').value     = c.nome;
        document.getElementById('cliente-cpf').value      = c.cpf      || '';
        document.getElementById('cliente-email').value    = c.email    || '';
        document.getElementById('cliente-telefone').value = c.telefone || '';
        document.getElementById('cliente-endereco').value = c.endereco || '';
    } else {
        title.innerHTML = '<i class="fas fa-user-plus"></i> Novo Cliente';
    }
    document.getElementById('modal-cliente').style.display = 'flex';
}

function fecharModalCliente() {
    document.getElementById('modal-cliente').style.display = 'none';
}

function limparFormCliente() {
    ['cliente-id','cliente-nome','cliente-cpf','cliente-email','cliente-telefone','cliente-endereco']
        .forEach(id => { const el = document.getElementById(id); if (el) el.value = id === 'cliente-id' ? '0' : ''; });
}

async function salvarCliente() {
    const id = parseInt(document.getElementById('cliente-id').value) || 0;
    const payload = {
        nome:     document.getElementById('cliente-nome').value.trim(),
        cpf:      document.getElementById('cliente-cpf').value.trim(),
        email:    document.getElementById('cliente-email').value.trim(),
        telefone: document.getElementById('cliente-telefone').value.trim(),
        endereco: document.getElementById('cliente-endereco').value.trim(),
    };
    if (!payload.nome) { mostrarAlerta('Informe o nome do cliente.', 'aviso'); return; }

    try {
        const url  = id ? `${API_BASE}api_clientes.php?action=atualizar&id=${id}` : `${API_BASE}api_clientes.php?action=criar`;
        const r    = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
        const data = await r.json();
        if (data.success) { mostrarAlerta(data.message, 'sucesso'); fecharModalCliente(); carregarClientes(); }
        else mostrarAlerta(data.message, 'erro');
    } catch { mostrarAlerta('Falha de conexão.', 'erro'); }
}

function editarCliente(id) {
    const c = clientesData.find(x => x.id == id);
    if (c) abrirModalCliente(c);
}

async function deletarCliente(id) {
    const ok = await confirmarAcao('Deseja realmente excluir este cliente?');
    if (!ok) return;
    const r    = await fetch(`${API_BASE}api_clientes.php?action=deletar&id=${id}`);
    const data = await r.json();
    if (data.success) { mostrarAlerta(data.message, 'sucesso'); carregarClientes(); }
    else mostrarAlerta(data.message, 'erro');
}
