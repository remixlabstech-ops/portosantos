/* Saidas JS */
let saidasData       = [];
let rateioRows       = [];

document.addEventListener('DOMContentLoaded', () => {
    carregarSaidas();
    carregarCategoriasDespesa();
    carregarCentrosCusto();
    initModalSaida();
});

// ── Load List ─────────────────────────────────────────────
function carregarSaidas(filtros = {}) {
    mostrarCarregando(document.getElementById('tabela-saidas'));
    const params = new URLSearchParams({ action: 'listar', ...filtros }).toString();
    fetch(`${API_BASE}api_saidas.php?${params}`)
        .then(r => r.json())
        .then(data => {
            saidasData = data.data || [];
            renderTabelaSaidas(saidasData);
        })
        .catch(() => mostrarAlerta('Erro ao carregar saídas', 'erro'));
}

function renderTabelaSaidas(lista) {
    const container = document.getElementById('tabela-saidas');
    if (!container) return;

    if (!lista.length) {
        container.innerHTML = '<p style="text-align:center;padding:30px;color:var(--text-muted)"><i class="fas fa-inbox"></i> Nenhuma saída encontrada.</p>';
        return;
    }

    const rows = lista.map(s => `
        <tr>
            <td>${formatarData(s.data_saida)}</td>
            <td>${s.fornecedor_nome || '<span class="text-muted">—</span>'}</td>
            <td>${s.categoria_nome || '—'}</td>
            <td>${s.centro_custo_nome || '—'}</td>
            <td class="fw-bold" style="color:var(--danger)">${formatarMoeda(s.valor)}</td>
            <td>${formatarData(s.data_vencimento)}</td>
            <td>${badgeSaida(s.status)}</td>
            <td>
                <button class="btn-acao-edit" onclick="editarSaida(${s.id})" title="Editar"><i class="fas fa-edit"></i></button>
                <button class="btn-acao-delete" onclick="deletarSaida(${s.id})" title="Excluir"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('');

    container.innerHTML = `<table>
        <thead><tr>
            <th>Data</th><th>Fornecedor</th><th>Categoria</th><th>Centro Custo</th>
            <th>Valor</th><th>Vencimento</th><th>Status</th><th>Ações</th>
        </tr></thead>
        <tbody>${rows}</tbody>
    </table>`;
}

function badgeSaida(status) {
    const map = { 'Aberto': 'badge-aberto', 'Pago': 'badge-pago', 'Cancelado': 'badge-cancelado' };
    return `<span class="badge ${map[status] || ''}">${status}</span>`;
}

// ── Filters ───────────────────────────────────────────────
document.getElementById('btn-filtrar')?.addEventListener('click', () => {
    const filtros = {};
    const di = document.getElementById('filtro-data-inicio')?.value;
    const df = document.getElementById('filtro-data-fim')?.value;
    const st = document.getElementById('filtro-status')?.value;
    if (di) filtros.data_inicio = di;
    if (df) filtros.data_fim    = df;
    if (st) filtros.status      = st;
    carregarSaidas(filtros);
});

document.getElementById('btn-limpar-filtro')?.addEventListener('click', () => {
    ['filtro-data-inicio','filtro-data-fim','filtro-status'].forEach(id => {
        const el = document.getElementById(id); if (el) el.value = '';
    });
    carregarSaidas();
});

// ── Selects ───────────────────────────────────────────────
function carregarCategoriasDespesa() {
    fetch(`${API_BASE}api_categorias.php?action=listar`)
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById('saida-categoria');
            if (!sel) return;
            const despesas = data.despesa || [];
            sel.innerHTML = '<option value="">Selecione...</option>' +
                despesas.map(c => `<option value="${c.id}">${c.nome}</option>`).join('');
        });
}

function carregarCentrosCusto() {
    fetch(`${API_BASE}api_centros_custo.php?action=listar`)
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById('saida-centro-custo');
            if (!sel) return;
            const lista = data.data || [];
            sel.innerHTML = '<option value="">Nenhum</option>' +
                lista.map(c => `<option value="${c.id}">${c.nome}</option>`).join('');
        });
}

// ── Modal ─────────────────────────────────────────────────
function initModalSaida() {
    document.getElementById('btn-nova-saida')?.addEventListener('click', () => abrirModalSaida());
    document.getElementById('fechar-modal-saida')?.addEventListener('click', fecharModalSaida);
    document.getElementById('cancelar-modal-saida')?.addEventListener('click', fecharModalSaida);
    document.getElementById('salvar-saida')?.addEventListener('click', salvarSaida);

    // Taxa
    document.getElementById('saida-tipo-taxa')?.addEventListener('change', function () {
        document.getElementById('row-taxa').style.display = this.value ? 'flex' : 'none';
    });

    // Tipo rateio
    document.querySelectorAll('input[name="tipo_rateio"]').forEach(r => {
        r.addEventListener('change', onTipoRateioChange);
    });

    document.getElementById('btn-add-rateio')?.addEventListener('click', addRateioRow);

    // Autocomplete fornecedor
    const inputForn = document.getElementById('saida-fornecedor-nome');
    if (inputForn) {
        initAutocomplete(inputForn, `${API_BASE}api_fornecedores.php?action=buscar&q=`, sel => {
            document.getElementById('saida-fornecedor-id').value = sel.id;
        });
    }

    // Autocomplete rateio cliente (único)
    const inputRC = document.getElementById('rateio-cliente-nome');
    if (inputRC) {
        initAutocomplete(inputRC, `${API_BASE}api_clientes.php?action=buscar&q=`, sel => {
            document.getElementById('rateio-cliente-id').value = sel.id;
        });
    }
}

function onTipoRateioChange() {
    const val = document.querySelector('input[name="tipo_rateio"]:checked')?.value;
    document.getElementById('rateio-cliente').style.display   = (val === 'cliente')   ? 'block' : 'none';
    document.getElementById('rateio-multiplos').style.display = (val === 'multiplos') ? 'block' : 'none';
}

function addRateioRow() {
    const container = document.getElementById('rateio-rows');
    const rowId = Date.now();
    const row = document.createElement('div');
    row.className = 'rateio-row';
    row.dataset.rowid = rowId;
    row.innerHTML = `
        <div class="autocomplete-wrapper" style="flex:1">
            <input type="text" class="form-control rateio-nome" placeholder="Buscar cliente..." autocomplete="off">
            <input type="hidden" class="rateio-cid">
            <div class="autocomplete-dropdown"></div>
        </div>
        <input type="number" class="form-control rateio-pct" placeholder="%" min="0" max="100" step="0.01">
        <button type="button" class="btn-acao-delete" onclick="removerRateioRow(${rowId})"><i class="fas fa-times"></i></button>
    `;
    container.appendChild(row);

    const input = row.querySelector('.rateio-nome');
    const cidInput = row.querySelector('.rateio-cid');
    initAutocomplete(input, `${API_BASE}api_clientes.php?action=buscar&q=`, sel => {
        cidInput.value = sel.id;
    });

    row.querySelector('.rateio-pct').addEventListener('input', atualizarSomaRateio);
}

function removerRateioRow(rowId) {
    document.querySelector(`.rateio-row[data-rowid="${rowId}"]`)?.remove();
    atualizarSomaRateio();
}

function atualizarSomaRateio() {
    const inputs = document.querySelectorAll('.rateio-pct');
    const soma   = Array.from(inputs).reduce((s, i) => s + (parseFloat(i.value) || 0), 0);
    const el     = document.getElementById('rateio-soma-display');
    if (el) {
        el.textContent = `Soma: ${soma.toFixed(2)}%`;
        el.className   = `rateio-soma ${Math.abs(soma - 100) < 0.01 ? 'valid' : soma > 100 ? 'invalid' : ''}`;
    }
}

function abrirModalSaida(saida = null) {
    limparFormSaida();
    const title = document.getElementById('modal-saida-title');
    if (saida) {
        title.innerHTML = '<i class="fas fa-edit"></i> Editar Saída';
        preencherFormSaida(saida);
    } else {
        title.innerHTML = '<i class="fas fa-plus-circle"></i> Nova Saída';
    }
    document.getElementById('modal-saida').style.display = 'flex';
}

function fecharModalSaida() {
    document.getElementById('modal-saida').style.display = 'none';
}

function limparFormSaida() {
    document.getElementById('saida-id').value = '0';
    document.getElementById('saida-fornecedor-nome').value = '';
    document.getElementById('saida-fornecedor-id').value   = '';
    document.getElementById('saida-categoria').value     = '';
    document.getElementById('saida-centro-custo').value  = '';
    document.getElementById('saida-valor').value         = '';
    document.getElementById('saida-data').value          = new Date().toISOString().slice(0, 10);
    document.getElementById('saida-vencimento').value    = '';
    document.getElementById('saida-status').value        = 'Aberto';
    document.getElementById('saida-parcelas').value      = '1';
    document.getElementById('saida-descricao').value     = '';
    document.getElementById('saida-tipo-taxa').value     = '';
    document.getElementById('row-taxa').style.display    = 'none';
    document.getElementById('rateio-cliente').style.display   = 'none';
    document.getElementById('rateio-multiplos').style.display = 'none';
    document.getElementById('rateio-rows').innerHTML = '';
    document.querySelector('input[name="tipo_rateio"][value="administrativo"]').checked = true;
}

function preencherFormSaida(s) {
    document.getElementById('saida-id').value = s.id;
    document.getElementById('saida-fornecedor-nome').value = s.fornecedor_nome || '';
    document.getElementById('saida-fornecedor-id').value   = s.fornecedor_id  || '';
    document.getElementById('saida-categoria').value    = s.categoria_id    || '';
    document.getElementById('saida-centro-custo').value = s.centro_custo_id || '';
    document.getElementById('saida-valor').value        = s.valor || '';
    document.getElementById('saida-data').value         = s.data_saida       || '';
    document.getElementById('saida-vencimento').value   = s.data_vencimento  || '';
    document.getElementById('saida-status').value       = s.status           || 'Aberto';
    document.getElementById('saida-parcelas').value     = s.num_parcelas     || '1';
    document.getElementById('saida-descricao').value    = s.descricao        || '';

    if (s.tipo_rateio) {
        const radio = document.querySelector(`input[name="tipo_rateio"][value="${s.tipo_rateio}"]`);
        if (radio) { radio.checked = true; onTipoRateioChange(); }
    }
}

async function salvarSaida() {
    const id  = parseInt(document.getElementById('saida-id').value) || 0;
    const tipo_rateio = document.querySelector('input[name="tipo_rateio"]:checked')?.value || 'administrativo';

    // Build rateios
    let rateios = [];
    if (tipo_rateio === 'cliente') {
        const cid = document.getElementById('rateio-cliente-id').value;
        if (cid) rateios = [{ cliente_id: cid, percentual: 100 }];
    } else if (tipo_rateio === 'multiplos') {
        document.querySelectorAll('.rateio-row').forEach(row => {
            const cid = row.querySelector('.rateio-cid')?.value;
            const pct = parseFloat(row.querySelector('.rateio-pct')?.value) || 0;
            if (cid && pct > 0) rateios.push({ cliente_id: cid, percentual: pct });
        });
        const soma = rateios.reduce((s, r) => s + r.percentual, 0);
        if (Math.abs(soma - 100) > 0.01) {
            mostrarAlerta(`A soma dos rateios deve ser 100%. Atual: ${soma.toFixed(2)}%`, 'aviso'); return;
        }
    }

    const payload = {
        fornecedor_id:   document.getElementById('saida-fornecedor-id').value || null,
        categoria_id:    document.getElementById('saida-categoria').value,
        centro_custo_id: document.getElementById('saida-centro-custo').value  || null,
        valor:           document.getElementById('saida-valor').value,
        taxa:            document.getElementById('saida-taxa')?.value         || null,
        tipo_taxa:       document.getElementById('saida-tipo-taxa').value     || null,
        data_saida:      document.getElementById('saida-data').value,
        data_vencimento: document.getElementById('saida-vencimento').value    || null,
        status:          document.getElementById('saida-status').value,
        num_parcelas:    document.getElementById('saida-parcelas').value,
        descricao:       document.getElementById('saida-descricao').value,
        tipo_rateio,
        rateios,
    };

    if (!payload.categoria_id) { mostrarAlerta('Selecione uma categoria.', 'aviso'); return; }
    if (!payload.descricao)    { mostrarAlerta('Informe a descrição.',      'aviso'); return; }

    try {
        const url  = id ? `${API_BASE}api_saidas.php?action=atualizar&id=${id}` : `${API_BASE}api_saidas.php?action=criar`;
        const r    = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(id ? { ...payload, id } : payload) });
        const data = await r.json();
        if (data.success) { mostrarAlerta(data.message, 'sucesso'); fecharModalSaida(); carregarSaidas(); }
        else mostrarAlerta(data.message || 'Erro ao salvar.', 'erro');
    } catch { mostrarAlerta('Falha de conexão.', 'erro'); }
}

function editarSaida(id) {
    const s = saidasData.find(x => x.id == id);
    if (s) abrirModalSaida(s);
}

async function deletarSaida(id) {
    const ok = await confirmarAcao('Deseja realmente excluir esta saída?');
    if (!ok) return;
    const r    = await fetch(`${API_BASE}api_saidas.php?action=deletar&id=${id}`);
    const data = await r.json();
    if (data.success) { mostrarAlerta(data.message, 'sucesso'); carregarSaidas(); }
    else mostrarAlerta(data.message, 'erro');
}
