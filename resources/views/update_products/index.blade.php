@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">

        <div class="a-page-head">
            <h1>Atualizar produtos</h1>
        </div>

        <div class="dist-grid">
            <div class="a-card dist-card">
                <div class="dist-card__icon"><ion-icon name="server-outline"></ion-icon></div>
                <h3>Distribuidora Soollar</h3>
                <div class="image-wrapper">
                    <img src="{{ asset('img/update_products/soollar.png') }}" alt="Soollar">
                </div>
                <a id="btn-soollar" class="a-btn-primary">Atualizar kits</a>
            </div>
            <div class="a-card dist-card">
                <div class="dist-card__icon"><ion-icon name="server-outline"></ion-icon></div>
                <h3>Distribuidora Edeltec</h3>
                <div class="image-wrapper">
                    <img src="{{ asset('img/update_products/edeltec.png') }}" alt="Edeltec">
                </div>
                <a id="btn-edeltec" class="a-btn-primary">Atualizar kits</a>
            </div>
        </div>

        <div id="progress-container" class="mt-5" style="display: none;">
            <x-progress-bar id="update-progress-bar" />
        </div>

        <div class="a-logblock mt-5">
            <div class="is-flex is-justify-content-space-between is-align-items-center" style="margin-bottom:10px;">
                <div class="a-logblock__title">Log de atualização</div>
                <a id="btn-clear-terminal" class="a-btn-ghost" style="padding:.4rem .85rem; font-size:.75rem;">Limpar</a>
            </div>
            <div id="terminal-log" style="white-space: pre-wrap; color: #DDD8C9; min-height: 100px; font-family: monospace; font-size: 12.5px; line-height: 1.6;">
                Alluz Energia® 2026 - Todos os direitos reservados
                ----------------
            </div>
        </div>

    </div>
    <style>
        .dist-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .dist-card { display: flex; flex-direction: column; align-items: center; text-align: center; gap: 14px; }
        .dist-card h3 { font-size: 16px; font-weight: 700 !important; }
        .dist-card__icon {
            width: 56px; height: 56px; border-radius: 16px; background: #FDECC5;
            display: flex; align-items: center; justify-content: center; color: #B9740A;
        }
        .dist-card__icon ion-icon { font-size: 26px; }
        .image-wrapper { display: flex; justify-content: center; align-items: center; height: 80px; width: 100%; }
        .image-wrapper img { max-height: 100%; max-width: 100%; object-fit: contain; }
        @media (max-width: 900px) { .dist-grid { grid-template-columns: 1fr; } }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Seletores de Elementos ---
            const terminal = document.getElementById('terminal-log');
            const soollarBtn = document.getElementById('btn-soollar');
            const edeltecBtn = document.getElementById('btn-edeltec');
            const clearBtn = document.getElementById('btn-clear-terminal');
            const progressContainer = document.getElementById('progress-container');

            // CAPTURA DO TOKEN CSRF (Substitui o localStorage)
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const updateButtons = [soollarBtn, edeltecBtn];
            let pollingInterval = null;

            // --- Funções Auxiliares ---

            function clearTerminal() {
                terminal.innerHTML = 'Alluz Energia® 2025 - Todos os direitos reservados\n----------------';
                if (progressContainer) {
                    progressContainer.style.display = 'none';
                }
            }

            function updateTerminal(message, color = '#FFFFFF') {
                const pre = document.createElement('pre');
                pre.style.whiteSpace = 'pre-wrap';
                pre.style.color = color;
                pre.style.fontFamily = 'monospace';
                pre.style.backgroundColor = 'transparent';
                pre.textContent = '\n' + message;
                terminal.appendChild(pre);
                terminal.scrollTop = terminal.scrollHeight;
            }

            function toggleButtons(disabled) {
                updateButtons.forEach(button => {
                    if (disabled) { button.classList.add('is-loading'); }
                    else { button.classList.remove('is-loading'); }
                    button.disabled = disabled;
                });
            }

            function stopPolling() {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
            }

            function updateProgressBar(data) {
                const progressBarContainer = document.getElementById('update-progress-bar');
                if (!progressContainer || !progressBarContainer || !data || data.status === 'IDLE') {
                    if (progressContainer) progressContainer.style.display = 'none';
                    return;
                }
                progressContainer.style.display = 'block';
                let progress = 0;
                let barClass = '';

                if (data.status === 'PROCESSING') {
                    progress = 25;
                    if ((data.created_products > 0 || data.updated_products > 0)) { progress = 50; }
                    if (data.created_kits > 0 || data.updated_kits > 0) { progress = 75; }
                } else if (data.status === 'SUCCESS') {
                    progress = 100;
                    barClass = 'is-success';
                } else if (data.status === 'ERROR') {
                    progress = 100;
                    barClass = 'is-danger';
                }

                const innerBars = progressBarContainer.querySelectorAll('.progress-bar-inner');
                innerBars.forEach(bar => {
                    bar.style.width = `${progress}%`;
                    bar.classList.remove('is-success', 'is-danger');
                    if (barClass) { bar.classList.add(barClass); }
                });
            }

            function displayProcessStatus(data) {
                if (!data || data.status === 'IDLE') { return; }
                const elapsedTime = data.status !== 'PROCESSING' ? `\nTempo decorrido: ${data.elapsed_time} segundos` : '';
                const message = `\n--- Status da Atualização Soollar ---\nStatus: ${data.status}\nData de Início: ${new Date(data.date).toLocaleString('pt-BR')}\n-------------------------------------\nProdutos Criados: ${data.created_products || 0}\nProdutos Atualizados: ${data.updated_products || 0}\nKits Criados: ${data.created_kits || 0}\nKits Atualizados: ${data.updated_kits || 0}\n${elapsedTime}\n-------------------------------------\n`;
                terminal.innerHTML = 'Alluz Energia® 2025 - Todos os direitos reservados\n----------------';
                updateTerminal(message.trim());
            }

            // --- Lógica de Polling e Requisição ---

            async function checkStatus() {
                try {
                    // Rotas GET não precisam de CSRF, mas usamos o header de Accept JSON
                    const response = await fetch('/api/soollar/update-status', {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) return;

                    const data = await response.json();
                    updateProgressBar(data);
                    displayProcessStatus(data);

                    if (data.status === 'SUCCESS' || data.status === 'ERROR') {
                        stopPolling();
                        toggleButtons(false);
                        updateTerminal(data.status === 'SUCCESS' ? '✅ Processo concluído!' : '❌ Erro no processo.', data.status === 'ERROR' ? '#ff6b6b' : '#FFFFFF');
                    } else if (data.status === 'PROCESSING') {
                        toggleButtons(true);
                    }
                } catch (error) {
                    console.error('Erro ao verificar status:', error);
                }
            }

            function startPolling() {
                stopPolling();
                pollingInterval = setInterval(checkStatus, 3000);
            }

            async function handleUpdate(endpoint) {
                if (!csrfToken) {
                    updateTerminal('❌ Erro: Token CSRF não encontrado. Verifique o base.blade.php.', '#ff6b6b');
                    return;
                }

                toggleButtons(true);
                clearTerminal();
                updateTerminal('Iniciando comunicação com o servidor...');

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken, // Envia a prova de autenticação para o Laravel
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                    });

                    const contentType = response.headers.get("content-type");

                    if (response.ok && contentType?.includes("application/json")) {
                        const data = await response.json();
                        updateTerminal(`✅ ${data.message}`);
                        startPolling();
                    } else {
                        const errorMsg = `Erro ${response.status}: Resposta inesperada do servidor.`;
                        throw new Error(errorMsg);
                    }

                } catch (error) {
                    updateTerminal(`❌ Falha ao iniciar: ${error.message}`, '#ff6b6b');
                    toggleButtons(false);
                }
            }

            // --- Event Listeners ---
            clearBtn.addEventListener('click', clearTerminal);
            soollarBtn.addEventListener('click', () => handleUpdate('/api/soollar/update-products'));
            edeltecBtn.addEventListener('click', () => alert('Funcionalidade Edeltec em desenvolvimento.'));

            // Verifica status ao carregar a página
            checkStatus();
        });
    </script>
@endsection
