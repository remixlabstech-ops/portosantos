/* Categorias JS */
let receitaData = [];
let despesaData = [];

document.addEventListener('DOMContentLoaded', () => {
    carregarCategorias();
    initModais();
});

function carregarCategorias() {
    fetch(`${API_BASE}api_categorias.php?action=listar`)
        .then(r => r.json())
        .then(data => {
            receitaData = data.receita || [];
            despesaData = data.despesa || [];
            renderReceita(receitaData);
            renderDespesa(despesaData);
        })
        .catch(() => mostrarAlerta('Erro ao carregar categorias', 'erro'));
}

function renderReceita(lista) {
    const el = document.getElementById('tabela-receita');
    if (!el) return;
    if (!lista.length) { el.innerHTML = '<p style="padding:14px;color:var(--text-muted)">Nenhuma categoria.</p>'; return; }
    el.innerHTML = `<table>
        <thead><tr><th>Nome</th><th>Área</th><th>Ações</th></tr></thead>
        <tbody>${lista.map(c => `<tr>
            <td>${c.nome}</td>
            <td><span class="badge badge-aberto">${c.tipo}</span></td>
            <td>
                <button class="btn-acao-edit" onclick="editarReceita(${c.id})"><i class="fas fa-edit"></i></button>
                <button class="btn-acao-delete" onclick="deletarCategoria(${c.id},'receita')"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('')}</tbody>
    </table>`;
}

function renderDespesa(lista) {
    const el = document.getElementById('tabela-despesa');
    if (!el) return;
    if (!lista.length) { el.innerHTML = '<p style="padding:14px;color:var(--text-muted)">Nenhuma categoria.</p>'; return; }
    el.innerHTML = `<table>
        <thead><tr><th>Nome</th><th>Ações</th></tr></thead>
        <tbody>${lista.map(c => `<tr>
            <td>${c.nome}</td>
            <td>
                <button class="btn-acao-edit" onclick="editarDespesa(${c.id})"><i class="fas fa-edit"></i></button>
                <button class="btn-acao-delete" onclick="deletarCategoria(${c.id},'despesa')"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('')}</tbody>
    </table>`;
}

function initModais() {
    // Receita
    document.getElementById('btn-nova-receita')?.addEventListener('click', () => {
        document.getElementById('receita-id').value = '0';
        document.getElementById('receita-nome').value = '';
        document.getElementById('modal-receita-title').textContent = 'Nova Categoria de Receita';
        document.getElementById('modal-receita').style.display = 'flex';
    });
    document.getElementById('fechar-modal-receita')?.addEventListener('click', () => { document.getElementById('modal-receita').style.display = 'none'; });
    document.getElementById('cancelar-modal-receita')?.addEventListener('click', () => { document.getElementById('modal-receita').style.display = 'none'; });
    document.getElementById('salvar-receita')?.addEventListener('click', salvarReceita);

    // Despesa
    document.getElementById('btn-nova-despesa')?.addEventListener('click', () => {
        document.getElementById('despesa-id').value = '0';
        document.getElementById('despesa-nome').value = '';
        document.getElementById('modal-despesa-title').textContent = 'Nova Categoria de Despesa';
        document.getElementById('modal-despesa').style.display = 'flex';
    });
    document.getElementById('fechar-modal-despesa')?.addEventListener('click', () => { document.getElementById('modal-despesa').style.display = 'none'; });
    document.getElementById('cancelar-modal-despesa')?.addEventListener('click', () => { document.getElementById('modal-despesa').style.display = 'none'; });
    document.getElementById('salvar-despesa')?.addEventListener('click', salvarDespesa);
}

async function salvarReceita() {
    const id   = parseInt(document.getElementById('receita-id').value) || 0;
    const nome = document.getElementById('receita-nome').value.trim();
    const tipo = document.getElementById('receita-tipo').value;
    if (!nome) { mostrarAlerta('Informe o nome.', 'aviso'); return; }

    const payload = { nome, tipo, tipo_categoria: 'receita' };
    const url = id ? `${API_BASE}api_categorias.php?action=atualizar&id=${id}` : `${API_BASE}api_categorias.php?action=criar`;
    const r = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
    const data = await r.json();
    if (data.success) { mostrarAlerta(data.message, 'sucesso'); document.getElementById('modal-receita').style.display = 'none'; carregarCategorias(); }
    else mostrarAlerta(data.message, 'erro');
}

async function salvarDespesa() {
    const id   = parseInt(document.getElementById('despesa-id').value) || 0;
    const nome = document.getElementById('despesa-nome').value.trim();
    if (!nome) { mostrarAlerta('Informe o nome.', 'aviso'); return; }

    const payload = { nome, tipo_categoria: 'despesa' };
    const url = id ? `${API_BASE}api_categorias.php?action=atualizar&id=${id}` : `${API_BASE}api_categorias.php?action=criar`;
    const r = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
    const data = await r.json();
    if (data.success) { mostrarAlerta(data.message, 'sucesso'); document.getElementById('modal-despesa').style.display = 'none'; carregarCategorias(); }
    else mostrarAlerta(data.message, 'erro');
}

function editarReceita(id) {
    const c = receitaData.find(x => x.id == id);
    if (!c) return;
    document.getElementById('receita-id').value   = c.id;
    document.getElementById('receita-nome').value = c.nome;
    document.getElementById('receita-tipo').value = c.tipo;
    document.getElementById('modal-receita-title').textContent = 'Editar Categoria de Receita';
    document.getElementById('modal-receita').style.display = 'flex';
}

function editarDespesa(id) {
    const c = despesaData.find(x => x.id == id);
    if (!c) return;
    document.getElementById('despesa-id').value   = c.id;
    document.getElementById('despesa-nome').value = c.nome;
    document.getElementById('modal-despesa-title').textContent = 'Editar Categoria de Despesa';
    document.getElementById('modal-despesa').style.display = 'flex';
}

async function deletarCategoria(id, tipo) {
    const ok = await confirmarAcao('Deseja realmente excluir esta categoria?');
    if (!ok) return;
    const r = await fetch(`${API_BASE}api_categorias.php?action=deletar&id=${id}&tipo_categoria=${tipo}`);
    const data = await r.json();
    if (data.success) { mostrarAlerta(data.message, 'sucesso'); carregarCategorias(); }
    else mostrarAlerta(data.message, 'erro');
}
