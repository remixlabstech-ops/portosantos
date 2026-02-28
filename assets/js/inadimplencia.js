/* Inadimplência JS - updated to use API_BASE */

document.addEventListener('DOMContentLoaded', () => {
    carregarInadimplencia(0);
    carregarFaixasInadimplencia();
    carregarRankingInadimplentes();
    initFiltrosBotoes();
});

function initFiltrosBotoes() {
    document.querySelectorAll('.btn-dias').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.btn-dias').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            carregarInadimplencia(parseInt(this.dataset.dias) || 0);
        });
    });

    document.getElementById('btn-dias-personalizado')?.addEventListener('click', () => {
        const dias = parseInt(document.getElementById('dias-personalizado').value) || 0;
        document.querySelectorAll('.btn-dias').forEach(b => b.classList.remove('active'));
        carregarInadimplencia(dias);
    });
}

function carregarInadimplencia(dias) {
    const container = document.getElementById('tabela-inadimplencia');
    if (container) mostrarCarregando(container);

    const tipo_filtro = dias > 0 ? dias : 'todos';

    fetch(`${API_BASE}api_inadimplencia.php?action=listar&tipo_filtro=${tipo_filtro}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                preencherTabelaInadimplencia(data.data);
                atualizarCards(data.data);
            } else {
                mostrarAlerta('Erro ao carregar inadimplências', 'erro');
            }
        })
        .catch(erro => {
            console.error('Erro:', erro);
            mostrarAlerta('Erro ao carregar inadimplências', 'erro');
        });
}

function atualizarCards(dados) {
    const total  = dados.reduce((s, i) => s + parseFloat(i.valor_entrada || 0), 0);
    const maior  = dados.reduce((m, i) => Math.max(m, parseFloat(i.valor_entrada || 0)), 0);

    const elTotal = document.getElementById('total-inadimplente');
    const elQtd   = document.getElementById('qtd-inadimplente');
    const elMaior = document.getElementById('maior-inadimplente');

    if (elTotal) elTotal.textContent = formatarMoeda(total);
    if (elQtd)   elQtd.textContent   = dados.length;
    if (elMaior) elMaior.textContent = formatarMoeda(maior);
}

function preencherTabelaInadimplencia(dados) {
    const container = document.getElementById('tabela-inadimplencia');
    if (!container) return;

    if (!dados.length) {
        container.innerHTML = '<p style="text-align:center;padding:30px;color:var(--accent)"><i class="fas fa-check-circle"></i> <strong>Nenhuma inadimplência encontrada!</strong></p>';
        return;
    }

    const rows = dados.map(item => {
        const dias = parseInt(item.dias_vencido);
        let badgeClass = 'badge-aviso';
        if (dias > 90)      badgeClass = 'badge-erro';
        else if (dias > 60) badgeClass = 'badge-vencido';

        return `<tr>
            <td><strong>${item.cliente_nome}</strong></td>
            <td>${item.cpf || '—'}</td>
            <td>${item.categoria_nome || '—'}</td>
            <td>${item.tipo_honorario || '—'}</td>
            <td class="fw-bold" style="color:var(--danger)">${formatarMoeda(item.valor_entrada)}</td>
            <td>${formatarData(item.data_vencimento)}</td>
            <td><span class="badge ${badgeClass}">${dias} dias</span></td>
            <td>
                <button class="btn-pequeno" onclick="marcarRecebido(${item.id})">
                    <i class="fas fa-check"></i> Receber
                </button>
            </td>
        </tr>`;
    }).join('');

    container.innerHTML = `<table>
        <thead><tr>
            <th>Cliente</th><th>CPF</th><th>Categoria</th><th>Tipo</th>
            <th>Valor</th><th>Vencimento</th><th>Dias Vencido</th><th>Ações</th>
        </tr></thead>
        <tbody>${rows}</tbody>
    </table>`;
}

function carregarFaixasInadimplencia() {
    fetch(`${API_BASE}api_inadimplencia.php?action=faixas`)
        .then(res => res.json())
        .then(data => { if (data.success) preencherTabelaFaixas(data.data); })
        .catch(erro => console.error('Erro faixas:', erro));
}

function preencherTabelaFaixas(faixas) {
    const container = document.getElementById('tabela-faixas-inadimplencia');
    if (!container) return;

    const rows = faixas.filter(f => f.quantidade > 0).map(f => `
        <tr>
            <td>Vencido há <strong>${f.faixa}</strong></td>
            <td>${f.quantidade}</td>
            <td>${formatarMoeda(f.valor_total || 0)}</td>
        </tr>`).join('');

    if (!rows) { container.innerHTML = ''; return; }

    container.innerHTML = `
        <div class="table-card">
            <div class="table-card-header"><h3><i class="fas fa-chart-bar"></i> Faixas de Inadimplência</h3></div>
            <table>
                <thead><tr><th>Faixa</th><th>Quantidade</th><th>Valor Total</th></tr></thead>
                <tbody>${rows}</tbody>
            </table>
        </div>`;
}

function carregarRankingInadimplentes() {
    fetch(`${API_BASE}api_inadimplencia.php?action=ranking`)
        .then(res => res.json())
        .then(data => { if (data.success) preencherRanking(data.data); })
        .catch(erro => console.error('Erro ranking:', erro));
}

function preencherRanking(lista) {
    const container = document.getElementById('ranking-inadimplentes');
    if (!container || !lista.length) return;

    const rows = lista.map((r, i) => `
        <tr>
            <td><strong>${i + 1}</strong></td>
            <td>${r.cliente_nome}</td>
            <td>${r.cpf || '—'}</td>
            <td>${r.quantidade}</td>
            <td>${formatarMoeda(r.valor_total)}</td>
            <td><span class="badge badge-erro">${r.max_dias} dias</span></td>
        </tr>`).join('');

    container.innerHTML = `
        <div class="table-card">
            <div class="table-card-header"><h3><i class="fas fa-trophy"></i> Ranking de Inadimplentes</h3></div>
            <table>
                <thead><tr><th>#</th><th>Cliente</th><th>CPF</th><th>Qtd.</th><th>Total</th><th>Maior Atraso</th></tr></thead>
                <tbody>${rows}</tbody>
            </table>
        </div>`;
}

async function marcarRecebido(id) {
    const ok = await confirmarAcao('Marcar esta entrada como Recebida?');
    if (!ok) return;

    const r    = await fetch(`${API_BASE}api_entradas.php?action=atualizar&id=${id}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: 'Recebido' }),
    });
    const data = await r.json();
    if (data.success) {
        mostrarAlerta('Entrada marcada como Recebida!', 'sucesso');
        carregarInadimplencia(0);
        carregarFaixasInadimplencia();
        carregarRankingInadimplentes();
    } else {
        mostrarAlerta(data.message || 'Erro ao atualizar.', 'erro');
    }
}
