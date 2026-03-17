<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seja um Agente Alluz</title>
    <style>
        :root {
            --bg: #0f172a;
            --card: #ffffff;
            --primary: #f59e0b;
            --primary-dark: #d97706;
            --secondary: #1e293b;
            --muted: #64748b;
            --border: #e2e8f0;
            --success: #16a34a;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, Arial, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(245, 158, 11, 0.32), transparent 45%),
                radial-gradient(circle at bottom left, rgba(59, 130, 246, 0.16), transparent 40%),
                var(--bg);
            color: #f8fafc;
        }

        .container {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 1100px;
            border-radius: 20px;
            overflow: hidden;
            background: var(--card);
            color: #0f172a;
            display: grid;
            grid-template-columns: minmax(280px, 430px) 1fr;
            box-shadow: 0 24px 55px rgba(2, 6, 23, 0.45);
        }

        .left-panel {
            background: linear-gradient(170deg, #f59e0b 0%, #f97316 100%);
            color: #fff;
            padding: 36px 30px;
            position: relative;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .logo-mark {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            display: grid;
            place-items: center;
            font-weight: 800;
        }

        .headline {
            font-size: 2rem;
            line-height: 1.15;
            margin: 0 0 14px;
        }

        .subheadline {
            margin: 0;
            line-height: 1.55;
            opacity: 0.96;
        }

        .steps {
            margin-top: 32px;
            display: grid;
            gap: 12px;
        }

        .step {
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 12px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(2px);
        }

        .step strong {
            display: block;
            font-size: 0.95rem;
        }

        .step span {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .step-index {
            width: 24px;
            height: 24px;
            border-radius: 999px;
            background: #fff;
            color: #ea580c;
            font-weight: 700;
            display: grid;
            place-items: center;
            font-size: 0.82rem;
            flex-shrink: 0;
        }

        .right-panel {
            padding: 36px;
        }

        .flow-title {
            margin: 0;
            font-size: 1.5rem;
            color: var(--secondary);
        }

        .flow-subtitle {
            margin: 8px 0 0;
            color: var(--muted);
            line-height: 1.45;
        }

        .progress-track {
            margin: 24px 0 16px;
            height: 8px;
            border-radius: 999px;
            background: #f1f5f9;
            overflow: hidden;
        }

        .progress-value {
            width: 33.33%;
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
        }

        .step-chip {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: #fff7ed;
            color: #9a3412;
            font-size: 0.84rem;
            font-weight: 600;
            margin-bottom: 14px;
        }

        form {
            display: grid;
            gap: 14px;
        }

        label {
            display: block;
            font-weight: 600;
            font-size: 0.94rem;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-size: 0.95rem;
        }

        input:focus {
            outline: 0;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
        }

        .error {
            color: #b91c1c;
            font-size: 0.85rem;
            margin-top: -6px;
        }

        .btn {
            margin-top: 10px;
            border: 0;
            border-radius: 12px;
            padding: 13px;
            font-weight: 700;
            font-size: 0.96rem;
            color: #fff;
            background: linear-gradient(160deg, var(--primary), #ea580c);
            cursor: pointer;
            transition: transform 0.15s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .trust {
            margin-top: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 0.84rem;
        }

        .trust-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: var(--success);
        }

        @media (max-width: 900px) {
            .card {
                grid-template-columns: 1fr;
            }

            .left-panel,
            .right-panel {
                padding: 30px 22px;
            }

            .headline {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <aside class="left-panel">
            <div class="logo">
                <div class="logo-mark">A</div>
                Alluz Energia
            </div>

            <h1 class="headline">Seu cadastro em 3 passos simples</h1>
            <p class="subheadline">
                Inspirado na experiência do nosso CRM, criamos um fluxo rápido para você entrar no programa de agentes
                e começar a vender com suporte completo.
            </p>

            <div class="steps">
                <div class="step">
                    <div class="step-index">1</div>
                    <div>
                        <strong>Preencha seus dados</strong>
                        <span>Leva menos de 1 minuto.</span>
                    </div>
                </div>
                <div class="step">
                    <div class="step-index">2</div>
                    <div>
                        <strong>Análise do perfil</strong>
                        <span>Nossa equipe valida e direciona seu atendimento.</span>
                    </div>
                </div>
                <div class="step">
                    <div class="step-index">3</div>
                    <div>
                        <strong>Ativação no CRM</strong>
                        <span>Você recebe contato com os próximos passos.</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="right-panel">
            <h2 class="flow-title">Quero me tornar agente Alluz</h2>
            <p class="flow-subtitle">Comece pelo passo 1 e envie seu cadastro para nossa equipe.</p>

            <div class="progress-track">
                <div class="progress-value"></div>
            </div>

            <div class="step-chip">Passo 1 de 3 · Dados de contato</div>

            <form method="POST" action="{{ route('landingpage.store') }}">
                @csrf

                <div>
                    <label for="name">Nome completo</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                </div>
                @error('name') <div class="error">{{ $message }}</div> @enderror

                <div>
                    <label for="email">E-mail profissional</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                </div>
                @error('email') <div class="error">{{ $message }}</div> @enderror

                <div>
                    <label for="phone_number">WhatsApp</label>
                    <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number') }}" required>
                </div>
                @error('phone_number') <div class="error">{{ $message }}</div> @enderror

                <button type="submit" class="btn">Avançar para análise</button>
            </form>

            <div class="trust">
                <span class="trust-dot"></span>
                Seus dados são enviados com segurança diretamente para o CRM de Agentes.
            </div>
        </main>
    </div>
</div>
</body>
</html>
