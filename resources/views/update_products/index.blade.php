@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <div class="columns mt-2 mb-5 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt="..">
                    Atualização de kits
                </h3>
            </div>
            <div class="columns">
                <div class="column is-6">
                    <div class="box is-flex is-flex-direction-column is-align-items-center is-justify-content-center">
                        <h3 class="is-size-4 has-text-centered mb-4">Distribuidora Soollar</h3>
                        <div class="image-wrapper">
                            <img src="{{ asset('img/update_products/soollar.png') }}" alt="no-image">
                        </div>
                        <a id="btn-soollar" class="button is-primary mt-4">Atualizar kits</a>
                    </div>
                </div>
                <div class="column is-6">
                    <div class="box is-flex is-flex-direction-column is-align-items-center is-justify-content-center">
                        <h3 class="is-size-4 has-text-centered mb-4">Distribuidora Edeltec</h3>
                        <div class="image-wrapper">
                            <img src="{{ asset('img/update_products/edeltec.png') }}" alt="no-image">
                        </div>
                        <a id="btn-edeltec" class="button is-primary mt-4">Atualizar kits</a>
                    </div>
                </div>
            </div>

            <div id="progress-container" class="mt-5" style="display: none;">
                <x-progress-bar id="update-progress-bar" />
            </div>

            <div class="box mt-5">
                <div class="is-flex is-justify-content-space-between is-align-items-center">
                    <h4 class="title is-4">Log de Atualização</h4>
                    <a id="btn-clear-terminal" class="button is-danger is-small">Limpar Log</a>
                </div>
                <div id="terminal-log" style="white-space: pre-wrap; background-color: #333; color: #FFFFFF; padding: 15px; border-radius: 5px; min-height: 100px;">
                    Alluz Energia® 2025 - Todos os direitos reservados
                    ----------------
                </div>
            </div>

        </div>
    </div>
    <style>
        .image-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100px;
            width: 100%;
        }

        .image-wrapper img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }
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
