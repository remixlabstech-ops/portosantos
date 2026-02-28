/* Entradas JS */
let entradasData     = [];
let tiposHonorarios  = [];
let categoriasReceita = [];

document.addEventListener('DOMContentLoaded', () => {
    carregarEntradas();
    carregarCategorias();
    carregarTiposHonorarios();
    initModalEntrada();
    initClienteRapido();
});

// ── Load List ─────────────────────────────────────────────
function carregarEntradas(filtros = {}) {
    mostrarCarregando(document.getElementById('tabela-entradas'));
    const params = new URLSearchParams({ action: 'listar', ...filtros }).toString();
    fetch(`${API_BASE}api_entradas.php?${params}`)
        .then(r => r.json())
        .then(data => {
            entradasData = data.data || [];
            renderTabelaEntradas(entradasData);
        })
        .catch(() => mostrarAlerta('Erro ao carregar entradas', 'erro'));
}

function renderTabelaEntradas(lista) {
    const container = document.getElementById('tabela-entradas');
    if (!container) return;

    if (!lista.length) {
        container.innerHTML = '<p style="text-align:center;padding:30px;color:var(--text-muted)"><i class="fas fa-inbox"></i> Nenhuma entrada encontrada.</p>';
        return;
    }

    const rows = lista.map(e => `
        <tr>
            <td>${formatarData(e.data_entrada)}</td>
            <td><strong>${e.cliente_nome || '—'}</strong><br><small class="text-muted">${e.cpf || ''}</small></td>
            <td>${e.categoria_nome || '—'}</td>
            <td>${e.tipo_honorario || '—'}</td>
            <td class="fw-bold">${formatarMoeda(e.valor_entrada)}</td>
            <td>${formatarData(e.data_vencimento)}</td>
            <td>${badgeStatus(e.status, 'entrada')}</td>
            <td>
                <button class="btn-acao-edit" onclick="editarEntrada(${e.id})" title="Editar"><i class="fas fa-edit"></i></button>
                <button class="btn-acao-delete" onclick="deletarEntrada(${e.id})" title="Excluir"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('');

    container.innerHTML = `<table>
        <thead><tr>
            <th>Data</th><th>Cliente</th><th>Categoria</th><th>Tipo</th>
            <th>Valor</th><th>Vencimento</th><th>Status</th><th>Ações</th>
        </tr></thead>
        <tbody>${rows}</tbody>
    </table>`;
}

function badgeStatus(status, tipo) {
    const map = {
        'Aberto':    'badge-aberto',
        'Recebido':  'badge-recebido',
        'Pago':      'badge-pago',
        'Cancelado': 'badge-cancelado',
    };
    return `<span class="badge ${map[status] || ''}">${status}</span>`;
}

// ── Filters ───────────────────────────────────────────────
document.getElementById('btn-filtrar')?.addEventListener('click', () => {
    const filtros = {};
    const di = document.getElementById('filtro-data-inicio')?.value;
    const df = document.getElementById('filtro-data-fim')?.value;
    const st = document.getElementById('filtro-status')?.value;
    const vmin = document.getElementById('filtro-valor-min')?.value;
    const vmax = document.getElementById('filtro-valor-max')?.value;
    if (di) filtros.data_inicio = di;
    if (df) filtros.data_fim   = df;
    if (st) filtros.status     = st;
    if (vmin) filtros.valor_min = vmin;
    if (vmax) filtros.valor_max = vmax;
    carregarEntradas(filtros);
});

document.getElementById('btn-limpar-filtro')?.addEventListener('click', () => {
    ['filtro-data-inicio','filtro-data-fim','filtro-status','filtro-valor-min','filtro-valor-max']
        .forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
    carregarEntradas();
});

// ── Categories / Tipos ────────────────────────────────────
function carregarCategorias() {
    fetch(`${API_BASE}api_categorias.php?action=listar`)
        .then(r => r.json())
        .then(data => {
            categoriasReceita = data.receita || [];
            const sel = document.getElementById('entrada-categoria');
            if (!sel) return;
            sel.innerHTML = '<option value="">Selecione...</option>' +
                categoriasReceita.map(c => `<option value="${c.id}">${c.nome} (${c.tipo})</option>`).join('');
        });
}

function carregarTiposHonorarios() {
    fetch(`${API_BASE}api_entradas.php?action=listar`)  // will be refactored; get tipos from categorias endpoint
        .catch(() => {});

    // Get tipos directly
    const pdo_fallback = [
        { id: 1, nome: 'Contratual' },
        { id: 2, nome: 'Avulso' },
        { id: 3, nome: 'Sucumbência' },
        { id: 4, nome: 'Êxito' },
    ];
    tiposHonorarios = pdo_fallback;
    const sel = document.getElementById('entrada-tipo-honorario');
    if (!sel) return;
    sel.innerHTML = '<option value="">Selecione...</option>' +
        tiposHonorarios.map(t => `<option value="${t.id}">${t.nome}</option>`).join('');
}

// ── Modal ─────────────────────────────────────────────────
function initModalEntrada() {
    document.getElementById('btn-nova-entrada')?.addEventListener('click', () => abrirModalEntrada());
    document.getElementById('fechar-modal-entrada')?.addEventListener('click', fecharModalEntrada);
    document.getElementById('cancelar-modal-entrada')?.addEventListener('click', fecharModalEntrada);
    document.getElementById('salvar-entrada')?.addEventListener('click', salvarEntrada);

    // Tipo honorario change
    document.getElementById('entrada-tipo-honorario')?.addEventListener('change', onTipoHonorarioChange);

    // Auto-calc
    document.getElementById('entrada-valor-causa')?.addEventListener('input', calcularValorAuto);
    document.getElementById('entrada-percentual')?.addEventListener('input', calcularValorAuto);

    // Parcelas preview
    document.getElementById('entrada-parcelas')?.addEventListener('input', previewParcelas);
    document.getElementById('entrada-vencimento')?.addEventListener('change', previewParcelas);

    // Autocomplete clientes
    const inputCliente = document.getElementById('entrada-cliente-nome');
    if (inputCliente) {
        initAutocomplete(inputCliente, `${API_BASE}api_clientes.php?action=buscar&q=`, sel => {
            document.getElementById('entrada-cliente-id').value = sel.id;
        });
    }
}

function abrirModalEntrada(entrada = null) {
    limparFormEntrada();
    const title = document.getElementById('modal-entrada-title');
    if (entrada) {
        title.innerHTML = '<i class="fas fa-edit"></i> Editar Entrada';
        preencherFormEntrada(entrada);
    } else {
        title.innerHTML = '<i class="fas fa-plus-circle"></i> Nova Entrada';
    }
    document.getElementById('modal-entrada').style.display = 'flex';
}

function fecharModalEntrada() {
    document.getElementById('modal-entrada').style.display = 'none';
}

function limparFormEntrada() {
    document.getElementById('entrada-id').value = '0';
    document.getElementById('entrada-cliente-nome').value = '';
    document.getElementById('entrada-cliente-id').value = '';
    document.getElementById('entrada-categoria').value = '';
    document.getElementById('entrada-tipo-honorario').value = '';
    document.getElementById('entrada-valor').value = '';
    document.getElementById('entrada-vencimento').value = '';
    document.getElementById('entrada-data').value = new Date().toISOString().slice(0, 10);
    document.getElementById('entrada-status').value = 'Aberto';
    document.getElementById('entrada-parcelas').value = '1';
    document.getElementById('entrada-descricao').value = '';
    document.getElementById('row-calculo-automatico').style.display = 'none';
    document.getElementById('preview-parcelas').innerHTML = '';
    document.getElementById('comprovante-atual').style.display = 'none';
}

function preencherFormEntrada(e) {
    document.getElementById('entrada-id').value = e.id;
    document.getElementById('entrada-cliente-nome').value = e.cliente_nome || '';
    document.getElementById('entrada-cliente-id').value = e.cliente_id || '';
    document.getElementById('entrada-categoria').value = e.categoria_id || '';
    document.getElementById('entrada-tipo-honorario').value = e.tipo_honorario_id || '';
    document.getElementById('entrada-valor').value = e.valor_entrada || '';
    document.getElementById('entrada-vencimento').value = e.data_vencimento || '';
    document.getElementById('entrada-data').value = e.data_entrada || '';
    document.getElementById('entrada-status').value = e.status || 'Aberto';
    document.getElementById('entrada-parcelas').value = e.num_parcelas || 1;
    document.getElementById('entrada-descricao').value = e.descricao || '';

    if (e.comprovante) {
        document.getElementById('comprovante-atual').style.display = 'block';
        document.getElementById('link-comprovante-atual').href = e.comprovante;
    }

    onTipoHonorarioChange();
}

function onTipoHonorarioChange() {
    const sel  = document.getElementById('entrada-tipo-honorario');
    const nome = sel.options[sel.selectedIndex]?.text || '';
    const show = ['Sucumbência', 'Êxito'].includes(nome);
    document.getElementById('row-calculo-automatico').style.display = show ? 'flex' : 'none';
    document.getElementById('entrada-valor').disabled = show;
    if (!show) document.getElementById('entrada-valor').disabled = false;
}

function calcularValorAuto() {
    const vc = parseFloat(document.getElementById('entrada-valor-causa').value) || 0;
    const pc = parseFloat(document.getElementById('entrada-percentual').value)  || 0;
    const resultado = (vc * pc / 100).toFixed(2);
    document.getElementById('entrada-valor-calculado-display').value = formatarMoeda(resultado);
    document.getElementById('entrada-valor').value = resultado;
}

function previewParcelas() {
    const n    = parseInt(document.getElementById('entrada-parcelas').value) || 1;
    const venc = document.getElementById('entrada-vencimento').value;
    const val  = parseFloat(document.getElementById('entrada-valor').value) || 0;
    const prev = document.getElementById('preview-parcelas');
    if (n <= 1 || !venc || !val) { prev.innerHTML = ''; return; }

    const parcVal = (val / n).toFixed(2);
    let html = '';
    let d = new Date(venc + 'T00:00:00');
    for (let i = 1; i <= n; i++) {
        html += `<span class="parcela-chip">${i}/${n}: ${formatarMoeda(parcVal)} — ${d.toLocaleDateString('pt-BR')}</span>`;
        d.setMonth(d.getMonth() + 1);
    }
    prev.innerHTML = html;
}

async function salvarEntrada() {
    const id = parseInt(document.getElementById('entrada-id').value) || 0;
    const payload = {
        cliente_id:        document.getElementById('entrada-cliente-id').value,
        categoria_id:      document.getElementById('entrada-categoria').value,
        tipo_honorario_id: document.getElementById('entrada-tipo-honorario').value,
        valor_entrada:     document.getElementById('entrada-valor').value,
        valor_causa:       document.getElementById('entrada-valor-causa')?.value || null,
        percentual:        document.getElementById('entrada-percentual')?.value  || null,
        data_entrada:      document.getElementById('entrada-data').value,
        data_vencimento:   document.getElementById('entrada-vencimento').value,
        status:            document.getElementById('entrada-status').value,
        num_parcelas:      document.getElementById('entrada-parcelas').value,
        descricao:         document.getElementById('entrada-descricao').value,
    };

    if (!payload.cliente_id) { mostrarAlerta('Selecione um cliente.', 'aviso'); return; }
    if (!payload.categoria_id) { mostrarAlerta('Selecione uma categoria.', 'aviso'); return; }
    if (!payload.tipo_honorario_id) { mostrarAlerta('Selecione o tipo de honorário.', 'aviso'); return; }

    const fileInput = document.getElementById('entrada-comprovante');
    let url, method, body;

    if (fileInput.files.length > 0) {
        const fd = new FormData();
        Object.entries(payload).forEach(([k, v]) => { if (v !== null && v !== '') fd.append(k, v); });
        fd.append('comprovante', fileInput.files[0]);
        url    = id ? `${API_BASE}api_entradas.php?action=atualizar&id=${id}` : `${API_BASE}api_entradas.php?action=criar`;
        body   = fd;
        method = 'POST';
    } else {
        url    = id ? `${API_BASE}api_entradas.php?action=atualizar&id=${id}` : `${API_BASE}api_entradas.php?action=criar`;
        body   = JSON.stringify(id ? { ...payload, id } : payload);
        method = 'POST';
    }

    try {
        const r    = await fetch(url, { method, body });
        const data = await r.json();
        if (data.success) {
            mostrarAlerta(data.message, 'sucesso');
            fecharModalEntrada();
            carregarEntradas();
        } else {
            mostrarAlerta(data.message || 'Erro ao salvar.', 'erro');
        }
    } catch {
        mostrarAlerta('Falha de conexão.', 'erro');
    }
}

function editarEntrada(id) {
    const e = entradasData.find(x => x.id == id);
    if (e) abrirModalEntrada(e);
}

async function deletarEntrada(id) {
    const ok = await confirmarAcao('Deseja realmente excluir esta entrada?');
    if (!ok) return;
    const r    = await fetch(`${API_BASE}api_entradas.php?action=deletar&id=${id}`);
    const data = await r.json();
    if (data.success) { mostrarAlerta(data.message, 'sucesso'); carregarEntradas(); }
    else mostrarAlerta(data.message, 'erro');
}

// ── Cliente Rápido ────────────────────────────────────────
function initClienteRapido() {
    document.getElementById('btn-novo-cliente-rapido')?.addEventListener('click', e => {
        e.preventDefault();
        document.getElementById('modal-cliente-rapido').style.display = 'flex';
    });
    document.getElementById('fechar-modal-cliente-rapido')?.addEventListener('click', () => {
        document.getElementById('modal-cliente-rapido').style.display = 'none';
    });
    document.getElementById('cancelar-cliente-rapido')?.addEventListener('click', () => {
        document.getElementById('modal-cliente-rapido').style.display = 'none';
    });
    document.getElementById('salvar-cliente-rapido')?.addEventListener('click', async () => {
        const nome = document.getElementById('cr-nome').value.trim();
        if (!nome) { mostrarAlerta('Informe o nome do cliente.', 'aviso'); return; }

        const payload = {
            nome,
            cpf:      document.getElementById('cr-cpf').value.trim(),
            telefone: document.getElementById('cr-telefone').value.trim(),
            email:    document.getElementById('cr-email').value.trim(),
        };

        const r    = await fetch(`${API_BASE}api_clientes.php?action=criar`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
        const data = await r.json();
        if (data.success) {
            mostrarAlerta('Cliente cadastrado!', 'sucesso');
            document.getElementById('entrada-cliente-nome').value = nome;
            document.getElementById('entrada-cliente-id').value   = data.id;
            document.getElementById('modal-cliente-rapido').style.display = 'none';
        } else {
            mostrarAlerta(data.message, 'erro');
        }
    });
}
