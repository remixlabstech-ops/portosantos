/* Centros de Custo JS */
let centrosData = [];

document.addEventListener('DOMContentLoaded', () => {
    carregarCentros();
    initModalCentro();
});

function carregarCentros() {
    mostrarCarregando(document.getElementById('tabela-centros'));
    fetch(`${API_BASE}api_centros_custo.php?action=listar`)
        .then(r => r.json())
        .then(data => { centrosData = data.data || []; renderTabelaCentros(centrosData); })
        .catch(() => mostrarAlerta('Erro ao carregar centros de custo', 'erro'));
}

function renderTabelaCentros(lista) {
    const container = document.getElementById('tabela-centros');
    if (!container) return;

    if (!lista.length) {
        container.innerHTML = '<p style="text-align:center;padding:30px;color:var(--text-muted)"><i class="fas fa-building"></i> Nenhum centro de custo cadastrado.</p>';
        return;
    }

    const rows = lista.map(c => `
        <tr>
            <td><strong>${c.nome}</strong></td>
            <td>${c.descricao || '—'}</td>
            <td>
                <button class="btn-acao-edit" onclick="editarCentro(${c.id})"><i class="fas fa-edit"></i></button>
                <button class="btn-acao-delete" onclick="deletarCentro(${c.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('');

    container.innerHTML = `<table>
        <thead><tr><th>Nome</th><th>Descrição</th><th>Ações</th></tr></thead>
        <tbody>${rows}</tbody>
    </table>`;
}

function initModalCentro() {
    document.getElementById('btn-novo-centro')?.addEventListener('click', () => abrirModalCentro());
    document.getElementById('fechar-modal-centro')?.addEventListener('click', fecharModalCentro);
    document.getElementById('cancelar-modal-centro')?.addEventListener('click', fecharModalCentro);
    document.getElementById('salvar-centro')?.addEventListener('click', salvarCentro);
}

function abrirModalCentro(c = null) {
    document.getElementById('centro-id').value = '0';
    document.getElementById('centro-nome').value = '';
    document.getElementById('centro-descricao').value = '';
    const title = document.getElementById('modal-centro-title');

    if (c) {
        title.innerHTML = '<i class="fas fa-edit"></i> Editar Centro de Custo';
        document.getElementById('centro-id').value       = c.id;
        document.getElementById('centro-nome').value     = c.nome;
        document.getElementById('centro-descricao').value= c.descricao || '';
    } else {
        title.innerHTML = '<i class="fas fa-building"></i> Novo Centro de Custo';
    }
    document.getElementById('modal-centro').style.display = 'flex';
}

function fecharModalCentro() {
    document.getElementById('modal-centro').style.display = 'none';
}

async function salvarCentro() {
    const id = parseInt(document.getElementById('centro-id').value) || 0;
    const payload = {
        nome:      document.getElementById('centro-nome').value.trim(),
        descricao: document.getElementById('centro-descricao').value.trim(),
    };
    if (!payload.nome) { mostrarAlerta('Informe o nome do centro de custo.', 'aviso'); return; }

    try {
        const url  = id ? `${API_BASE}api_centros_custo.php?action=atualizar&id=${id}` : `${API_BASE}api_centros_custo.php?action=criar`;
        const r    = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
        const data = await r.json();
        if (data.success) { mostrarAlerta(data.message, 'sucesso'); fecharModalCentro(); carregarCentros(); }
        else mostrarAlerta(data.message, 'erro');
    } catch { mostrarAlerta('Falha de conexão.', 'erro'); }
}

function editarCentro(id) {
    const c = centrosData.find(x => x.id == id);
    if (c) abrirModalCentro(c);
}

async function deletarCentro(id) {
    const ok = await confirmarAcao('Deseja realmente excluir este centro de custo?');
    if (!ok) return;
    const r    = await fetch(`${API_BASE}api_centros_custo.php?action=deletar&id=${id}`);
    const data = await r.json();
    if (data.success) { mostrarAlerta(data.message, 'sucesso'); carregarCentros(); }
    else mostrarAlerta(data.message, 'erro');
}
