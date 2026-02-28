/* ============================================================
   Porto Santos Advocacia — Global App Utilities
   ============================================================ */

const API_BASE = './api/';

// ── Theme ──────────────────────────────────────────────────
function initTheme() {
    const saved = localStorage.getItem('ps_theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
    updateThemeIcon(saved);
}

function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    const next = current === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('ps_theme', next);
    updateThemeIcon(next);
}

function updateThemeIcon(theme) {
    const icon = document.getElementById('themeIcon');
    if (!icon) return;
    icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
}

// ── Currency / Date Formatting ──────────────────────────────
function formatarMoeda(valor) {
    const num = parseFloat(valor) || 0;
    return num.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function formatarData(data) {
    if (!data) return '—';
    const d = new Date(data + 'T00:00:00');
    return d.toLocaleDateString('pt-BR');
}

// ── Notifications ───────────────────────────────────────────
function mostrarAlerta(mensagem, tipo = 'info') {
    const container = document.getElementById('notification-container');
    if (!container) return;

    const el = document.createElement('div');
    el.className = `notification ${tipo}`;

    const icons = { sucesso: 'fa-check-circle', erro: 'fa-times-circle', aviso: 'fa-exclamation-triangle', info: 'fa-info-circle' };
    el.innerHTML = `<i class="fas ${icons[tipo] || icons.info}"></i><span>${mensagem}</span>`;

    container.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .4s'; setTimeout(() => el.remove(), 420); }, 3800);
}

// ── Loading ─────────────────────────────────────────────────
function mostrarCarregando(container) {
    if (!container) return;
    container.innerHTML = '<div class="loading-spinner"></div>';
}

function ocultarCarregando(container) {
    if (!container) return;
    const spinner = container.querySelector('.loading-spinner');
    if (spinner) spinner.remove();
}

// ── Confirm Dialog ───────────────────────────────────────────
function confirmarAcao(mensagem) {
    return new Promise(resolve => {
        const dialog  = document.getElementById('confirm-dialog');
        const msgEl   = document.getElementById('confirm-message');
        const btnOk   = document.getElementById('confirm-ok');
        const btnCanc = document.getElementById('confirm-cancel');
        if (!dialog) { resolve(window.confirm(mensagem)); return; }

        msgEl.textContent = mensagem;
        dialog.style.display = 'flex';

        const cleanup = () => { dialog.style.display = 'none'; btnOk.onclick = null; btnCanc.onclick = null; };
        btnOk.onclick   = () => { cleanup(); resolve(true); };
        btnCanc.onclick = () => { cleanup(); resolve(false); };
    });
}

// ── Autocomplete ─────────────────────────────────────────────
function initAutocomplete(inputEl, apiUrl, onSelect) {
    let timer = null;
    const dropdown = inputEl.nextElementSibling && inputEl.nextElementSibling.classList.contains('autocomplete-dropdown')
        ? inputEl.nextElementSibling
        : inputEl.parentElement.querySelector('.autocomplete-dropdown');

    if (!dropdown) return;

    inputEl.addEventListener('input', () => {
        clearTimeout(timer);
        const q = inputEl.value.trim();
        if (q.length < 2) { dropdown.style.display = 'none'; return; }

        timer = setTimeout(() => {
            fetch(apiUrl + encodeURIComponent(q))
                .then(r => r.json())
                .then(data => {
                    if (!data.success || !data.data.length) { dropdown.style.display = 'none'; return; }
                    dropdown.innerHTML = data.data.map(item =>
                        `<div class="autocomplete-item" data-id="${item.id}" data-nome="${item.nome}">
                            ${item.nome}
                            <small>${item.cpf || item.cnpj || item.email || ''}</small>
                         </div>`
                    ).join('');
                    dropdown.style.display = 'block';

                    dropdown.querySelectorAll('.autocomplete-item').forEach(el => {
                        el.addEventListener('click', () => {
                            inputEl.value = el.dataset.nome;
                            dropdown.style.display = 'none';
                            onSelect({ id: el.dataset.id, nome: el.dataset.nome });
                        });
                    });
                })
                .catch(() => { dropdown.style.display = 'none'; });
        }, 280);
    });

    document.addEventListener('click', e => {
        if (!inputEl.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}

// ── Sidebar Mobile ───────────────────────────────────────────
function initSidebar() {
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (!toggle || !sidebar) return;

    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
    });
}

// ── Init ─────────────────────────────────────────────────────
function initApp() {
    initTheme();
    initSidebar();

    const themeBtn = document.getElementById('themeToggle');
    if (themeBtn) themeBtn.addEventListener('click', toggleTheme);
}

document.addEventListener('DOMContentLoaded', initApp);
