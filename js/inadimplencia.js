function carregarInadimplencia() {
    const tipoFiltro = document.getElementById('filtro-dias-inadimplencia')?.value || '30';
    
    mostrarCarregando(document.getElementById('tabela-inadimplencia'));
    
    fetch(`${API_URL}api_inadimplencia.php?action=listar&tipo_filtro=${tipoFiltro}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                preencherTabelaInadimplencia(data.data);
                carregarFaixasInadimplencia();
            }
        })
        .catch(erro => {
            console.error('Erro:', erro);
            mostrarAlerta('Erro ao carregar inadimplências', 'erro');
        });
}

function preencherTabelaInadimplencia(dados) {
    const container = document.getElementById('tabela-inadimplencia');
    if (!container) return;
    
    if (dados.length === 0) {
        container.innerHTML = '<p style="text-align: center; padding: 20px; color: #27ae60;"><strong>✓</strong> Nenhuma inadimplência encontrada!</p>';
        return;
    }
    
    let html = `
        <div class="tabela-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>CPF</th>
                        <th>Categoria</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Data Vencimento</th>
                        <th>Dias Vencido</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody
    `;
    
    dados.forEach(item => {
        const diasVencido = item.dias_vencido;
        let badgeClass = 'badge-aviso';
        
        if (diasVencido > 90) {
            badgeClass = 'badge-erro';
        } else if (diasVencido > 60) {
            badgeClass = 'badge-vencido';
        }
        
        html += `
            <tr>
                <td><strong>${item.cliente_nome}</strong></td>
                <td>${item.cpf || '-'}</td>
                <td>${item.categoria_nome}</td>
                <td>${item.tipo_honorario}</td>
                <td>${formatarMoeda(item.valor_entrada)}</td>
                <td>${formatarData(item.data_vencimento)}</td>
                <td><span class="badge ${badgeClass}">${diasVencido} dias</span></td>
                <td>
                    <button class="btn btn-pequeno" onclick="abrirConfirmacao(${item.id})">Cobrar</button>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

function carregarFaixasInadimplencia() {
    fetch(`${API_URL}api_inadimplencia.php?action=faixas`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                preencherTabelaFaixas(data.data);
            }
        })
        .catch(erro => console.error('Erro:', erro));
}

function preencherTabelaFaixas(faixas) {
    const container = document.getElementById('tabela-faixas-inadimplencia');
    if (!container) return;
    
    let html = `
        <h3>Faixas de Inadimplência</h3>
        <div class="tabela-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Faixa</th>
                        <th>Quantidade</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    faixas.forEach(faixa => {
        html += `
            <tr>
                <td><strong>Vencido há ${faixa.faixa}</strong></td>
                <td>${faixa.quantidade}</td>
                <td>${formatarMoeda(faixa.valor_total || 0)}</td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

function abrirConfirmacao(id) {
    // Abrir modal de confirmação de cobrança
    if (confirm('Marcar como cobrado?')) {
        marcarsemCobrado(id);
    }
}

function marcarsemCobrado(id) {
    // Implementar marcação como cobrado
    mostrarAlerta('Marcado como cobrado', 'sucesso');
}