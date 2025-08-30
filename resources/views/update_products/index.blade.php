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

            const updateButtons = [soollarBtn, edeltecBtn];
            let pollingInterval = null;

            // --- Funções Auxiliares ---

            /**
             * NOVA FUNÇÃO: Envia um erro para ser registrado no log do backend.
             */
            async function logErrorToServer(message, context = '') {
                try {
                    const token = localStorage.getItem('auth_token');
                    if (!token) return;

                    await fetch('/api/log-frontend-error', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        },
                        body: JSON.stringify({ message, context })
                    });
                } catch (e) {
                    console.error("Falha ao enviar o log para o servidor:", e);
                }
            }

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

            // --- Lógica Principal ---
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

            async function checkStatus() {
                const token = localStorage.getItem('auth_token');
                if (!token) { stopPolling(); return; }
                try {
                    const response = await fetch('/api/soollar/update-status', { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
                    if (!response.ok) {
                        console.error('Falha ao buscar status (não-OK):', response.statusText);
                        updateTerminal(`Falha ao obter status (${response.status}). Tentando novamente...`, '#f39c12');
                        return;
                    }
                    const data = await response.json();
                    updateProgressBar(data);
                    displayProcessStatus(data);
                    if (data.status === 'SUCCESS' || data.status === 'ERROR') {
                        stopPolling();
                        toggleButtons(false);
                        updateTerminal(data.status === 'SUCCESS' ? '✅ Processo concluído com sucesso!' : '❌ Processo finalizado com erro.', data.status === 'ERROR' ? '#ff6b6b' : '#FFFFFF');
                    } else if (data.status === 'PROCESSING') {
                        toggleButtons(true);
                    }
                } catch (error) {
                    console.error('Falha de conexão ao verificar status:', error);
                    updateTerminal(`Falha de conexão ao verificar status. A verificação continuará...`, '#f39c12');
                }
            }

            function startPolling() {
                stopPolling();
                pollingInterval = setInterval(checkStatus, 3000);
            }

            async function handleUpdate(endpoint) {
                toggleButtons(true);
                clearTerminal();
                updateTerminal('Iniciando processo de atualização em segundo plano...');
                if (progressContainer) {
                    progressContainer.style.display = 'block';
                    updateProgressBar({ status: 'PROCESSING', created_products: 0, updated_products: 0, created_kits: 0, updated_kits: 0 });
                }

                const token = localStorage.getItem('auth_token');
                if (!token) {
                    updateTerminal('\nErro: Token de autenticação não encontrado.', '#ff6b6b');
                    toggleButtons(false);
                    return;
                }

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    });

                    // >>>>>>>>>>>>>>>> INÍCIO DA CORREÇÃO <<<<<<<<<<<<<<<<<<<
                    const contentType = response.headers.get("content-type");
                    // Verifica se a resposta é bem-sucedida e se é JSON
                    if (response.ok && contentType && contentType.includes("application/json")) {
                        const data = await response.json(); // Só tenta o .json() se for seguro
                        updateTerminal(`✅ ${data.message}`);
                        updateTerminal('Aguardando o primeiro status...');
                        startPolling();
                    } else {
                        // Se não for JSON, trata como erro, lê como texto e envia para o log
                        const errorHtml = await response.text();
                        const errorMessage = `O servidor respondeu com um formato inesperado (Status: ${response.status}).`;

                        // Envia o log para o backend
                        await logErrorToServer(errorMessage, errorHtml);

                        // Lança um erro para ser pego pelo bloco catch e exibido na tela
                        throw new Error(errorMessage + " O erro foi registrado para análise.");
                    }
                    // >>>>>>>>>>>>>>>> FIM DA CORREÇÃO <<<<<<<<<<<<<<<<<<<

                } catch (error) {
                    // Este bloco 'catch' agora pega tanto erros de rede quanto o erro que lançamos acima
                    updateTerminal(`❌ Erro ao iniciar a atualização: ${error.message}`, '#ff6b6b');
                    toggleButtons(false);
                }
            }

            // --- Event Listeners ---
            clearBtn.addEventListener('click', clearTerminal);
            soollarBtn.addEventListener('click', () => handleUpdate('/api/soollar/update-products'));
            edeltecBtn.addEventListener('click', () => { alert('A funcionalidade para Edeltec ainda não foi implementada.'); });

            checkStatus().then(() => {
                if (!pollingInterval) {
                    toggleButtons(false);
                }
            });
        });
    </script>
@endsection
