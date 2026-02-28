/**
 * Porto Santos ERP - Entradas JS
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {
    initAutocomplete(
        'entrada-cliente-busca',
        'entrada-cliente-id',
        'entrada-cliente-sugestoes',
        '/api/api_clientes.php'
    );
    lucide.createIcons();
});

/* ── Modal Nova Entrada ─────────────────────────────────── */
function abrirModalEntrada() {
    document.getElementById('form-entrada')?.reset();
    document.getElementById('grupo-causa-perc').style.display = 'none';
    document.getElementById('preview-parcelas').innerHTML = '';
    abrirModal('modal-entrada');
}

/* ── Modal Cadastro Rápido Cliente ──────────────────────── */
function abrirModalCliente() {
    document.getElementById('form-cliente-rapido')?.reset();
    abrirModal('modal-cliente');
}

/* ── Regra automática Sucumbência/Êxito ─────────────────── */
function onChangeTipoHonorario(valor) {
    const grupo = document.getElementById('grupo-causa-perc');
    const inputValor = document.getElementById('entrada-valor');
    const tiposCalculo = ['Sucumbência', 'Êxito'];

    if (tiposCalculo.includes(valor)) {
        grupo.style.display = 'block';
        inputValor.readOnly = true;
        inputValor.style.background = 'var(--light-bg)';
    } else {
        grupo.style.display = 'none';
        inputValor.readOnly = false;
        inputValor.style.background = '';
    }
}

function calcularValorHonorario() {
    const causa = parseFloat(document.getElementById('entrada-valor-causa')?.value) || 0;
    const perc  = parseFloat(document.getElementById('entrada-percentual')?.value) || 0;
    const valor = (causa * perc) / 100;
    const input = document.getElementById('entrada-valor');
    if (input) input.value = valor.toFixed(2);
}

/* ── Preview de Parcelas ────────────────────────────────── */
function previewParcelas() {
    const n    = parseInt(document.getElementById('entrada-parcelas')?.value) || 1;
    const val  = parseFloat(document.getElementById('entrada-valor')?.value) || 0;
    const data = document.getElementById('entrada-data-vencimento')?.value;
    const cont = document.getElementById('preview-parcelas');
    if (!cont) return;

    if (n <= 1 || !val || !data) {
        cont.innerHTML = '';
        return;
    }

    const parcela = (val / n).toFixed(2);
    let html = '<p><strong>Preview das Parcelas:</strong></p><ul>';
    for (let i = 1; i <= n; i++) {
        const d = new Date(data + 'T00:00:00');
        d.setMonth(d.getMonth() + i);
        html += `<li>Parcela ${i}: R$ ${parcela} — ${d.toLocaleDateString('pt-BR')}</li>`;
    }
    html += '</ul>';
    cont.innerHTML = html;
}

/* ── Salvar Entrada ─────────────────────────────────────── */
document.getElementById('form-entrada')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form    = e.target;
    const formData = new FormData(form);
    const data    = Object.fromEntries(formData.entries());

    if (!data.cliente_id) {
        mostrarToast('Selecione um cliente', 'erro');
        return;
    }
    if (!data.valor || parseFloat(data.valor) <= 0) {
        mostrarToast('Informe um valor válido', 'erro');
        return;
    }

    try {
        const json = await apiFetch('/api/api_entradas.php?action=criar', {
            method: 'POST',
            body: JSON.stringify(data),
        });
        if (json.success) {
            mostrarToast('Entrada criada com sucesso!', 'sucesso');
            fecharModal('modal-entrada');
            setTimeout(() => location.reload(), 800);
        } else {
            mostrarToast(json.message || 'Erro ao salvar', 'erro');
        }
    } catch {
        mostrarToast('Erro de comunicação com o servidor', 'erro');
    }
});

/* ── Salvar Cliente Rápido ──────────────────────────────── */
document.getElementById('form-cliente-rapido')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target).entries());

    if (!data.nome?.trim()) {
        mostrarToast('Nome é obrigatório', 'erro');
        return;
    }

    const json = await apiFetch('/api/api_clientes.php?action=criar', {
        method: 'POST',
        body: JSON.stringify(data),
    });

    if (json.success) {
        mostrarToast('Cliente criado!', 'sucesso');
        // Preenche automaticamente o campo de busca
        document.getElementById('entrada-cliente-id').value = json.data.id;
        document.getElementById('entrada-cliente-busca').value = data.nome;
        fecharModal('modal-cliente');
    } else {
        mostrarToast(json.message || 'Erro ao salvar', 'erro');
    }
});
