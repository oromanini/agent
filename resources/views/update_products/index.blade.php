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

            <div class="box mt-5">
                <div class="is-flex is-justify-content-space-between is-align-items-center">
                    <h4 class="title is-4">Log de Atualização</h4>
                    <a id="btn-clear-terminal" class="button is-danger is-small">Limpar Log</a>
                </div>
                <div id="terminal-log" style="white-space: pre-wrap; background-color: #333; color: #00ff00; padding: 15px; border-radius: 5px;">
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
            const terminal = document.getElementById('terminal-log');
            const soollarBtn = document.getElementById('btn-soollar');
            const edeltecBtn = document.getElementById('btn-edeltec');
            const clearBtn = document.getElementById('btn-clear-terminal');

            const updateButtons = [soollarBtn, edeltecBtn];

            function updateTerminal(message) {
                terminal.innerHTML += '\n' + message;
                terminal.scrollTop = terminal.scrollHeight;
            }

            function updateTerminalWithData(data) {
                let message = `Resposta do servidor: (Status: ${data.status})`;

                if (data.status >= 200 && data.status < 300) {
                    message += `\nSucesso: ${data.message}`;
                } else {
                    // Mostra a mensagem principal do erro
                    message += `\nErro: ${data.message}`;

                    // Verifica se há um array de 'errors' e o exibe de forma detalhada
                    if (data.errors) {
                        const errorDetails = JSON.stringify(data.errors, null, 2); // Formata o objeto de erros
                        message += `\n\nDetalhes:\n${errorDetails}`;
                    }
                }

                if (data.total !== undefined) {
                    message += `\nTotal de kits: ${data.total}`;
                }

                const pre = document.createElement('pre');
                pre.style.color = '#00ff00';
                pre.style.backgroundColor = '#333333FF';
                pre.textContent = '\n' + message;
                terminal.appendChild(pre);
                terminal.scrollTop = terminal.scrollHeight;
//...
            }

            function clearTerminal() {
                terminal.innerHTML = 'Alluz Energia® 2025 - Todos os direitos reservados\n----------------';
            }

            function toggleButtons(disabled) {
                updateButtons.forEach(button => {
                    if (disabled) {
                        button.classList.add('is-loading');
                    } else {
                        button.classList.remove('is-loading');
                    }
                    button.disabled = disabled;
                });
            }

            async function handleUpdate(endpoint) {
                toggleButtons(true);

                const token = localStorage.getItem('auth_token');

                if (!token) {
                    updateTerminal('\nErro: Token de autenticação não encontrado. Tentando realizar login...');
                    await loginAndRunUpdate(endpoint);
                    return;
                }

                terminal.innerHTML = 'Iniciando atualização...';
                updateTerminal('Atualizando produtos. Por favor, aguarde...');

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`
                        },
                        credentials: 'omit'
                    });

                    const data = await response.json();

                    // Use a nova função para exibir os dados
                    updateTerminalWithData({ status: response.status, ...data });

                } catch (error) {
                    updateTerminal(`\nOcorreu um erro: ${error.message}`);
                } finally {
                    toggleButtons(false);
                }
            }

            async function loginAndRunUpdate(endpoint) {
                const loginEndpoint = '/api/authorize';
                const email = 'oscar.romanini@alluzenergia.com.br';
                const password = 'Neia@vida.2022!';

                try {
                    const response = await fetch(loginEndpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ email, password })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        localStorage.setItem('auth_token', data.access_token);
                        updateTerminal('Login bem-sucedido! Token salvo.');
                        await handleUpdate(endpoint);
                    } else {
                        updateTerminal(`\nErro no login: ${data.message || 'Credenciais inválidas.'}`);
                    }

                } catch (error) {
                    updateTerminal(`\nOcorreu um erro na requisição de login: ${error.message}`);
                }
            }

            clearBtn.addEventListener('click', clearTerminal);

            soollarBtn.addEventListener('click', () => {
                handleUpdate('/api/soollar/update-products');
            });

            edeltecBtn.addEventListener('click', () => {
                handleUpdate('/api/edeltec/update-products');
            });
        });
    </script>
@endsection
