/**
 * Porto Santos ERP - Saídas JS
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {
    initAutocomplete(
        'saida-fornecedor-busca',
        'saida-fornecedor-id',
        'saida-fornecedor-sugestoes',
        '/api/api_fornecedores.php'
    );
    lucide.createIcons();
});

/* ── Modal Nova Saída ───────────────────────────────────── */
function abrirModalSaida() {
    document.getElementById('form-saida')?.reset();
    document.getElementById('grupo-taxa-perc').style.display = 'none';
    abrirModal('modal-saida');
}

function abrirModalFornecedor() {
    abrirModal('modal-fornecedor-rapido');
}

/* ── Taxa Percentual/Valor Fixo ─────────────────────────── */
function onChangeTipoTaxa(valor) {
    const grupo = document.getElementById('grupo-taxa-perc');
    if (valor === 'percentual') {
        grupo.style.display = 'block';
    } else {
        grupo.style.display = 'none';
        document.getElementById('saida-valor')?.removeAttribute('readonly');
    }
}

function calcularValorSaida() {
    const base = parseFloat(document.getElementById('saida-valor-base')?.value) || 0;
    const taxa = parseFloat(document.getElementById('saida-taxa-valor')?.value) || 0;
    const val  = (base * taxa) / 100;
    const inp  = document.getElementById('saida-valor');
    if (inp) inp.value = val.toFixed(2);
}

/* ── Rateio ─────────────────────────────────────────────── */
function adicionarRateio() {
    const container = document.getElementById('rateio-container');
    if (!container) return;

    const div = document.createElement('div');
    div.className = 'rateio-item';
    div.innerHTML = `
        <select name="rateio_tipo[]" class="form-control form-control-sm">
            <option value="administrativo">Administrativo</option>
            <option value="cliente">Cliente Específico</option>
        </select>
        <input type="number" name="rateio_perc[]" placeholder="%" class="form-control form-control-sm" min="0" max="100" oninput="validarSomaRateio()">
        <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove(); validarSomaRateio()">
            &times;
        </button>
    `;
    container.appendChild(div);
}

function validarSomaRateio() {
    const inputs = document.querySelectorAll('[name="rateio_perc[]"]');
    const soma   = Array.from(inputs).reduce((acc, i) => acc + (parseFloat(i.value) || 0), 0);
    const aviso  = document.getElementById('rateio-soma-aviso');
    if (aviso) {
        aviso.style.display = Math.abs(soma - 100) < 0.01 || inputs.length === 0 ? 'none' : 'inline';
    }
}

/* ── Salvar Saída ───────────────────────────────────────── */
document.getElementById('form-saida')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data     = Object.fromEntries(formData.entries());

    // Monta rateios
    const tipos  = formData.getAll('rateio_tipo[]');
    const percs  = formData.getAll('rateio_perc[]');
    const rateios = tipos.map((t, i) => ({
        tipo_rateio:        t,
        tipo_divisao:       'percentual',
        percentual_divisao: parseFloat(percs[i]) || 0,
    }));

    // Remove campos de rateio do objeto principal
    delete data['rateio_tipo[]'];
    delete data['rateio_perc[]'];

    if (rateios.length > 0) {
        const soma = rateios.reduce((acc, r) => acc + r.percentual_divisao, 0);
        if (Math.abs(soma - 100) > 0.01) {
            mostrarToast('A soma dos rateios deve ser 100%', 'erro');
            return;
        }
        data.rateios = rateios;
    }

    if (!data.valor || parseFloat(data.valor) <= 0) {
        mostrarToast('Informe um valor válido', 'erro');
        return;
    }

    try {
        const json = await apiFetch('/api/api_saidas.php?action=criar', {
            method: 'POST',
            body: JSON.stringify(data),
        });
        if (json.success) {
            mostrarToast('Saída criada com sucesso!', 'sucesso');
            fecharModal('modal-saida');
            setTimeout(() => location.reload(), 800);
        } else {
            mostrarToast(json.message || 'Erro ao salvar', 'erro');
        }
    } catch {
        mostrarToast('Erro de comunicação com o servidor', 'erro');
    }
});
