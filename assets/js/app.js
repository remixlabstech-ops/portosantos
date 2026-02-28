/**
 * Porto Santos ERP - App Principal
 * Tema, Sidebar, Ações Globais
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initSidebar();
    initLucide();
});

/* ── Tema Claro/Escuro ──────────────────────────────────── */
function initTheme() {
    const btn      = document.getElementById('theme-toggle');
    const iconSun  = document.getElementById('icon-sun');
    const iconMoon = document.getElementById('icon-moon');

    const applyTheme = (dark) => {
        document.body.classList.toggle('dark-mode', dark);
        if (iconSun && iconMoon) {
            iconSun.style.display  = dark ? 'none'  : '';
            iconMoon.style.display = dark ? ''      : 'none';
        }
    };

    const saved = localStorage.getItem('theme') === 'dark';
    applyTheme(saved);

    btn?.addEventListener('click', () => {
        const dark = !document.body.classList.contains('dark-mode');
        applyTheme(dark);
        localStorage.setItem('theme', dark ? 'dark' : 'light');
    });
}

/* ── Sidebar Toggle ─────────────────────────────────────── */
function initSidebar() {
    const toggle  = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    if (!toggle || !sidebar) return;

    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });
}

/* ── Re-inicializa Lucide após mudanças DOM ─────────────── */
function initLucide() {
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

/* ── Modal Helpers (globais) ────────────────────────────── */
function abrirModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.style.display = 'flex';
    initLucide();
}

function fecharModal(id) {
    const m = document.getElementById(id);
    if (m) m.style.display = 'none';
}

/* ── Marcar Pago (entradas/saidas) ──────────────────────── */
async function marcarPago(modulo, id) {
    if (!confirm('Marcar como Pago?')) return;
    const apiMap = { entradas: '/api/api_entradas.php?action=atualizar_status', saidas: '/api/api_saidas.php?action=atualizar_status' };
    const url = apiMap[modulo];
    if (!url) return;
    const json = await apiFetch(url, { method: 'POST', body: JSON.stringify({ id, status: 'Pago' }) });
    if (json.success) {
        mostrarToast('Marcado como Pago!', 'sucesso');
        setTimeout(() => location.reload(), 800);
    } else {
        mostrarToast(json.message || 'Erro ao atualizar', 'erro');
    }
}

/* ── Confirmar Delete ───────────────────────────────────── */
async function confirmarDelete(modulo, id) {
    if (!confirm('Tem certeza que deseja remover este registro?')) return;
    const apiMap = {
        entradas:     '/api/api_entradas.php?action=deletar',
        saidas:       '/api/api_saidas.php?action=deletar',
        clientes:     '/api/api_clientes.php?action=deletar',
        fornecedores: '/api/api_fornecedores.php?action=deletar',
        centros_custo:'/api/api_centros_custo.php?action=deletar',
    };
    const baseUrl = apiMap[modulo];
    if (!baseUrl) return;
    const json = await apiFetch(`${baseUrl}&id=${id}`, { method: 'GET' });
    if (json.success) {
        mostrarToast('Registro removido!', 'sucesso');
        setTimeout(() => location.reload(), 800);
    } else {
        mostrarToast(json.message || 'Erro ao remover', 'erro');
    }
}
