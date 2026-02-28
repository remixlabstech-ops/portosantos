/* Dashboard JS */
let chartMensal = null;
let chartArea   = null;

document.addEventListener('DOMContentLoaded', () => {
    carregarDashboard();
});

function carregarDashboard() {
    fetch(API_BASE + 'api_dashboard.php')
        .then(r => r.json())
        .then(data => {
            if (!data.success) { mostrarAlerta('Erro ao carregar dashboard', 'erro'); return; }
            renderCards(data);
            renderGraficoMensal(data.grafico_mensal);
            renderGraficoArea(data.grafico_area);
            renderRankingClientes(data.ranking_clientes);
            renderRankingCentros(data.ranking_centros);
        })
        .catch(() => mostrarAlerta('Falha de conexão com a API', 'erro'));
}

function renderCards(data) {
    const resumo = data.resumo_mes || {};
    const inadimplencia = data.inadimplencia || {};

    setText('card-entradas', formatarMoeda(resumo.total_entradas));
    setText('card-saidas',   formatarMoeda(resumo.total_saidas));
    setText('card-lucro',    formatarMoeda(resumo.lucro_liquido));
    setText('card-receber',  formatarMoeda(data.total_receber));
    setText('card-pagar',    formatarMoeda(data.total_pagar));
    setText('card-inadimplencia', formatarMoeda(inadimplencia.total));
    setText('card-inadimplencia-qtd', `${inadimplencia.qtd || 0} registro(s)`);
}

function setText(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
}

function renderGraficoMensal(dados) {
    const ctx = document.getElementById('chartMensal');
    if (!ctx) return;
    const meses = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];

    if (chartMensal) chartMensal.destroy();
    chartMensal = new Chart(ctx, {
        type: 'line',
        data: {
            labels: meses,
            datasets: [
                {
                    label: 'Entradas',
                    data: dados?.entradas || Array(12).fill(0),
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39,174,96,.1)',
                    fill: true,
                    tension: .4,
                    pointRadius: 4,
                },
                {
                    label: 'Saídas',
                    data: dados?.saidas || Array(12).fill(0),
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231,76,60,.1)',
                    fill: true,
                    tension: .4,
                    pointRadius: 4,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + formatarMoeda(ctx.parsed.y)
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: v => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 0 })
                    }
                }
            }
        }
    });
}

function renderGraficoArea(dados) {
    const ctx = document.getElementById('chartArea');
    if (!ctx || !dados?.length) return;

    const labels = dados.map(d => d.area);
    const values = dados.map(d => parseFloat(d.total));
    const colors = ['#2980b9','#27ae60','#f39c12','#e74c3c','#9b59b6'];

    if (chartArea) chartArea.destroy();
    chartArea = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{ data: values, backgroundColor: colors.slice(0, labels.length), borderWidth: 2 }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12 } },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${formatarMoeda(ctx.parsed)}`
                    }
                }
            }
        }
    });
}

function renderRankingClientes(dados) {
    const el = document.getElementById('ranking-clientes');
    if (!el) return;
    if (!dados?.length) { el.innerHTML = '<p class="text-muted" style="padding:14px">Nenhum dado.</p>'; return; }

    el.innerHTML = `<table>
        <thead><tr><th>#</th><th>Cliente</th><th>Total Recebido</th></tr></thead>
        <tbody>
            ${dados.map((r, i) => `<tr>
                <td><strong>${i + 1}</strong></td>
                <td>${r.nome}</td>
                <td>${formatarMoeda(r.total)}</td>
            </tr>`).join('')}
        </tbody>
    </table>`;
}

function renderRankingCentros(dados) {
    const el = document.getElementById('ranking-centros');
    if (!el) return;
    if (!dados?.length) { el.innerHTML = '<p class="text-muted" style="padding:14px">Nenhum dado.</p>'; return; }

    el.innerHTML = `<table>
        <thead><tr><th>#</th><th>Centro de Custo</th><th>Total Gasto</th></tr></thead>
        <tbody>
            ${dados.map((r, i) => `<tr>
                <td><strong>${i + 1}</strong></td>
                <td>${r.nome}</td>
                <td>${formatarMoeda(r.total)}</td>
            </tr>`).join('')}
        </tbody>
    </table>`;
}
