/**
 * Porto Santos ERP - Gerenciamento de Modais
 */

'use strict';

/* ── Fechar modal com ESC ───────────────────────────────── */
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(m => {
            m.style.display = 'none';
        });
    }
});

/* ── Autocomplete genérico ──────────────────────────────── */

/**
 * Inicializa autocomplete em um input de busca.
 *
 * @param {string} inputId   - ID do input de texto
 * @param {string} hiddenId  - ID do input hidden (armazena ID selecionado)
 * @param {string} dropId    - ID do div de sugestões
 * @param {string} apiUrl    - URL da API de busca (deve aceitar ?action=buscar&q=termo)
 */
function initAutocomplete(inputId, hiddenId, dropId, apiUrl) {
    const input  = document.getElementById(inputId);
    const hidden = document.getElementById(hiddenId);
    const drop   = document.getElementById(dropId);
    if (!input || !hidden || !drop) return;

    let timer;

    input.addEventListener('input', () => {
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) {
            drop.style.display = 'none';
            hidden.value = '';
            return;
        }
        timer = setTimeout(async () => {
            const json = await apiFetch(`${apiUrl}?action=buscar&q=${encodeURIComponent(q)}`);
            if (!json.success || !json.data.length) {
                drop.style.display = 'none';
                return;
            }
            drop.innerHTML = '';
            json.data.forEach(item => {
                const div = document.createElement('div');
                div.className = 'ac-item';
                div.textContent = item.nome + (item.cpf ? ` — ${item.cpf}` : '') + (item.documento ? ` — ${item.documento}` : '');
                div.dataset.id   = item.id;
                div.dataset.nome = item.nome;
                div.addEventListener('click', () => {
                    input.value   = item.nome;
                    hidden.value  = item.id;
                    drop.style.display = 'none';
                });
                drop.appendChild(div);
            });
            drop.style.display = 'block';
        }, 250);
    });

    document.addEventListener('click', (e) => {
        if (!drop.contains(e.target) && e.target !== input) {
            drop.style.display = 'none';
        }
    });
}
