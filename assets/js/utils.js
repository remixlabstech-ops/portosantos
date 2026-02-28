/**
 * Porto Santos ERP - Utilitários JS
 */

'use strict';

/**
 * Formata valor em moeda BRL.
 * @param {number} valor
 * @returns {string}
 */
function formatarMoeda(valor) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(valor || 0);
}

/**
 * Formata data ISO para DD/MM/YYYY.
 * @param {string} data
 * @returns {string}
 */
function formatarData(data) {
    if (!data) return '—';
    const [y, m, d] = data.split('-');
    return `${d}/${m}/${y}`;
}

/**
 * Exibe toast de notificação.
 * @param {string} mensagem
 * @param {'sucesso'|'erro'|'aviso'|'info'} tipo
 */
function mostrarToast(mensagem, tipo = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const map = { sucesso: 'toast-success', erro: 'toast-error', aviso: 'toast-warning', info: 'toast-info' };
    const cls = map[tipo] || 'toast-info';

    const toast = document.createElement('div');
    toast.className = `toast ${cls}`;
    toast.textContent = mensagem;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity .3s';
        setTimeout(() => toast.remove(), 320);
    }, 3500);
}

/** Alias legado */
const mostrarAlerta = mostrarToast;

/**
 * Mostra spinner de carregamento dentro de um container.
 * @param {HTMLElement} el
 */
function mostrarCarregando(el) {
    if (!el) return;
    el.innerHTML = '<div style="text-align:center;padding:2rem"><span class="spinner"></span></div>';
}

/**
 * Realiza fetch JSON com cabeçalhos padrões.
 * @param {string} url
 * @param {RequestInit} [opts]
 * @returns {Promise<any>}
 */
async function apiFetch(url, opts = {}) {
    const defaults = {
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    };
    const resp = await fetch(url, { ...defaults, ...opts });
    const json = await resp.json();
    return json;
}
