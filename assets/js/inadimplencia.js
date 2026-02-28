/**
 * Porto Santos ERP - Inadimplência JS (versão completa)
 */

'use strict';

let filtroAtual = 'todos';

document.addEventListener('DOMContentLoaded', () => {
    carregarInadimplencia('todos');
    lucide.createIcons();

    document.getElementById('form-renegociar')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target).entries());
        const json = await apiFetch('/api/api_inadimplencia.php?action=renegociar', {
            method: 'POST',
            body: JSON.stringify(data),
        });
        if (json.success) {
            mostrarToast('Renegociado com sucesso!', 'sucesso');
            fecharModal('modal-renegociar');
            carregarInadimplencia(filtroAtual);
        } else {
            mostrarToast(json.message || 'Erro', 'erro');
        }
    });
});

/**
 * Carrega inadimplência da API.
 * @param {string|number} dias
 */
async function carregarInadimplencia(dias) {
    filtroAtual = dias;
    const container = document.getElementById('tabela-inadimplencia');
    mostrarCarregando(container);

    const json = await apiFetch(`/api/api_inadimplencia.php?action=listar&tipo_filtro=${dias}`);
    if (!json.success) {
        container.innerHTML = '<p class="text-center text-muted">Erro ao carregar dados.</p>';
        return;
    }

    // Indicadores
    document.getElementById('ind-total-valor').textContent = formatarMoeda(json.data.total_valor);
    document.getElementById('ind-media-dias').textContent  = json.data.media_dias + ' dias';
    document.getElementById('ind-maior-atraso').textContent= json.data.maior_atraso + ' dias';

    preencherTabelaInadimplencia(json.data.registros);
    carregarFaixasInadimplencia();
}

function preencherTabelaInadimplencia(dados) {
    const container = document.getElementById('tabela-inadimplencia');
    if (!container) return;

    if (!dados.length) {
        container.innerHTML = '<p style="text-align:center;padding:2rem;color:var(--success-color)"><strong>✓</strong> Nenhuma inadimplência encontrada!</p>';
        return;
    }

    let html = `
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>CPF</th>
                        <th>Categoria</th>
                        <th>Valor</th>
                        <th>Vencimento</th>
                        <th>Dias Vencido</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
    `;

    dados.forEach(item => {
        const dias = item.dias_vencido;
        let badgeClass = 'badge-aviso';
        if (dias > 90)  badgeClass = 'badge-erro';
        else if (dias > 60) badgeClass = 'badge-vencido';

        html += `
            <tr>
                <td><strong>${escapeHtml(item.cliente_nome || '—')}</strong></td>
                <td>${escapeHtml(item.cpf || '—')}</td>
                <td>${escapeHtml(item.categoria || '—')}</td>
                <td>${formatarMoeda(item.valor)}</td>
                <td>${formatarData(item.data_vencimento)}</td>
                <td><span class="badge ${badgeClass}">${dias} dias</span></td>
                <td class="actions-cell">
                    <button class="btn btn-sm btn-success" onclick="marcarPagoInad(${item.id})" title="Marcar como Pago">
                        ✓
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="abrirRenegociar(${item.id})" title="Renegociar">
                        ↺
                    </button>
                </td>
            </tr>
        `;
    });

    html += '</tbody></table></div>';
    container.innerHTML = html;
}

async function carregarFaixasInadimplencia() {
    const json = await apiFetch('/api/api_inadimplencia.php?action=faixas');
    if (!json.success) return;
    preencherTabelaFaixas(json.data);
}

function preencherTabelaFaixas(faixas) {
    const container = document.getElementById('tabela-faixas-inadimplencia');
    if (!container) return;

    let html = `
        <h3 class="card-title">Faixas de Inadimplência</h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr><th>Faixa</th><th>Quantidade</th><th>Valor Total</th></tr>
                </thead>
                <tbody>
    `;

    faixas.forEach(f => {
        html += `
            <tr>
                <td><strong>Vencido há ${escapeHtml(f.faixa)}</strong></td>
                <td>${f.quantidade}</td>
                <td>${formatarMoeda(f.valor_total)}</td>
            </tr>
        `;
    });

    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function filtrarInadimplencia(dias, btn) {
    document.querySelectorAll('.faixa-btn').forEach(b => b.classList.remove('active'));
    btn?.classList.add('active');
    carregarInadimplencia(dias);
}

function filtrarPersonalizado() {
    const inicio = document.getElementById('filtro-data-inicio')?.value;
    const fim    = document.getElementById('filtro-data-fim')?.value;
    if (!inicio && !fim) {
        mostrarToast('Informe ao menos uma data', 'aviso');
        return;
    }
    // Para filtro por data usa a diferença de dias calculada no backend com 'todos'
    // e filtra client-side ou usa o filtro padrão
    carregarInadimplencia('todos');
}

function limparFiltros() {
    document.querySelectorAll('.faixa-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.faixa-btn[data-dias="todos"]')?.classList.add('active');
    if (document.getElementById('filtro-data-inicio')) document.getElementById('filtro-data-inicio').value = '';
    if (document.getElementById('filtro-data-fim'))    document.getElementById('filtro-data-fim').value = '';
    carregarInadimplencia('todos');
}

async function marcarPagoInad(id) {
    if (!confirm('Marcar como Pago?')) return;
    const json = await apiFetch('/api/api_inadimplencia.php?action=marcar_pago', {
        method: 'POST',
        body: JSON.stringify({ id }),
    });
    if (json.success) {
        mostrarToast('Marcado como Pago!', 'sucesso');
        carregarInadimplencia(filtroAtual);
    } else {
        mostrarToast(json.message || 'Erro', 'erro');
    }
}

function abrirRenegociar(id) {
    document.getElementById('reneg-id').value = id;
    document.getElementById('reneg-nova-data').value = '';
    abrirModal('modal-renegociar');
}

/**
 * Escapa HTML para evitar XSS na exibição de dados.
 * @param {string} str
 * @returns {string}
 */
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
