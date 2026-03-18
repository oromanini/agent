<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seja um Agente Alluz</title>
    <!-- Meta Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '2181553886009307');
        fbq('track', 'PageView');
    </script>
    <!-- End Meta Pixel Code -->
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

        * { box-sizing: border-box; }

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
        }

        .logo { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 28px; font-weight: 700; }
        .logo-mark { width: 34px; height: 34px; border-radius: 8px; background: rgba(255, 255, 255, 0.2); display: grid; place-items: center; font-weight: 800; }
        .headline { font-size: 2rem; line-height: 1.15; margin: 0 0 14px; }
        .subheadline { margin: 0; line-height: 1.55; opacity: 0.96; }
        .steps { margin-top: 32px; display: grid; gap: 12px; }

        .step {
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 12px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .step strong { display: block; font-size: 0.95rem; }
        .step-index { width: 24px; height: 24px; border-radius: 999px; background: #fff; color: #ea580c; font-weight: 700; display: grid; place-items: center; font-size: 0.82rem; flex-shrink: 0; }

        .right-panel { padding: 36px; }
        .flow-title { margin: 0; font-size: 1.5rem; color: var(--secondary); }
        .flow-subtitle { margin: 8px 0 0; color: var(--muted); line-height: 1.45; }

        .progress-track { margin: 24px 0 16px; height: 8px; border-radius: 999px; background: #f1f5f9; overflow: hidden; }
        .progress-value { width: 25%; height: 100%; background: linear-gradient(90deg, var(--primary), var(--primary-dark)); transition: width .2s ease; }
        .step-chip { display: inline-flex; align-items: center; padding: 6px 10px; border-radius: 999px; background: #fff7ed; color: #9a3412; font-size: 0.84rem; font-weight: 600; margin-bottom: 14px; }

        form { display: grid; gap: 14px; }
        .form-step { display: none; }
        .form-step.is-active { display: block; }

        label { display: block; font-weight: 600; font-size: 0.94rem; margin-bottom: 6px; }
        input, select { width: 100%; padding: 12px; border-radius: 10px; border: 1px solid var(--border); font-size: 0.95rem; }
        input:focus, select:focus { outline: 0; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2); }

        .error { color: #b91c1c; font-size: 0.85rem; margin-top: -6px; }
        .actions { display: flex; gap: 10px; margin-top: 10px; }

        .btn {
            border: 0;
            border-radius: 12px;
            padding: 13px;
            font-weight: 700;
            font-size: 0.96rem;
            color: #fff;
            background: linear-gradient(160deg, var(--primary), #ea580c);
            cursor: pointer;
            width: 100%;
        }

        .btn-secondary { background: #e2e8f0; color: #334155; }

        .trust { margin-top: 14px; display: flex; align-items: center; gap: 8px; color: var(--muted); font-size: 0.84rem; }
        .trust-dot { width: 9px; height: 9px; border-radius: 999px; background: var(--success); }

        .agreement-modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.8);
            display: grid;
            place-items: center;
            padding: 18px;
            z-index: 9999;
        }

        .agreement-modal.is-hidden { display: none; }

        .agreement-box {
            width: min(100%, 480px);
            background: #fff;
            color: #0f172a;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 24px 55px rgba(2, 6, 23, 0.45);
            text-align: center;
        }

        .agreement-text { margin: 0 0 18px; font-size: 1.08rem; line-height: 1.45; }
        .agreement-actions { display: grid; gap: 10px; }

        .agreement-thank-you {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 700;
            color: #0f172a;
        }

        @media (max-width: 900px) {
            .card { grid-template-columns: 1fr; }
            .left-panel, .right-panel { padding: 30px 22px; }
            .right-panel {
                margin-top: -16px;
                border-radius: 24px 24px 0 0;
                background: var(--card);
            }
            .headline { font-size: 1.8rem; }
            .steps { display: none; }
        }
    </style>
</head>
<body>
<noscript>
    <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=2181553886009307&ev=PageView&noscript=1" />
</noscript>

<div class="agreement-modal" id="agreement-modal" role="dialog" aria-modal="true" aria-labelledby="agreement-text">
    <div class="agreement-box">
        <p class="agreement-text" id="agreement-text">Essa é uma posição 100% comissionada, e não uma vaga CLT.</p>
        <div class="agreement-actions" id="agreement-actions">
            <button type="button" class="btn" id="agree-button">Estou de acordo, continuar</button>
            <button type="button" class="btn btn-secondary" id="disagree-button">Não estou de acordo, sair</button>
        </div>
    </div>
</div>

<div class="container">
    <div class="card">
        <aside class="left-panel">
            <div class="logo"><div class="logo-mark">A</div>Alluz Energia</div>
            <h1 class="headline">Seu cadastro em 4 etapas simples</h1>
            <p class="subheadline">Preencha etapa por etapa e envie seu cadastro para o time de agentes.</p>

            <div class="steps">
                <div class="step"><div class="step-index">1</div><div><strong>Nome</strong></div></div>
                <div class="step"><div class="step-index">2</div><div><strong>Telefone</strong></div></div>
                <div class="step"><div class="step-index">3</div><div><strong>E-mail</strong></div></div>
                <div class="step"><div class="step-index">4</div><div><strong>Tempo de experiência com solar</strong></div></div>
            </div>
        </aside>

        <main class="right-panel">
            <h2 class="flow-title">Quero me tornar agente Alluz</h2>
            <p class="flow-subtitle">Siga as etapas abaixo para concluir o cadastro.</p>

            <div class="progress-track">
                <div class="progress-value" id="progress-value"></div>
            </div>

            <div class="step-chip" id="step-chip">Etapa 1 de 4 · Nome</div>

            <form method="POST" action="{{ route('landingpage.store') }}" id="landing-form">
                @csrf

                <div class="form-step is-active" data-step="1" data-label="Nome">
                    <label for="name">Etapa 1 - Nome</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                    @error('name') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-step" data-step="2" data-label="Telefone">
                    <label for="phone_number">Etapa 2 - Telefone</label>
                    <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number') }}" required maxlength="15" inputmode="numeric">
                    @error('phone_number') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-step" data-step="3" data-label="E-mail">
                    <label for="email">Etapa 3 - E-mail</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                    @error('email') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-step" data-step="4" data-label="Tempo de experiência com solar">
                    <label for="solar_experience">Etapa 4 - Tempo de experiência com solar</label>
                    <select id="solar_experience" name="solar_experience" required>
                        <option value="">Selecione</option>
                        <option value="sem_experiencia" @selected(old('solar_experience') === 'sem_experiencia')>Sem experiência</option>
                        <option value="ate_2_anos" @selected(old('solar_experience') === 'ate_2_anos')>Até 2 anos</option>
                        <option value="mais_2_anos" @selected(old('solar_experience') === 'mais_2_anos')>Mais de 2 anos</option>
                    </select>
                    @error('solar_experience') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="actions">
                    <button type="button" class="btn btn-secondary" id="prev-button" style="display:none;">Voltar</button>
                    <button type="button" class="btn" id="next-button">Próxima etapa</button>
                    <button type="submit" class="btn" id="submit-button" style="display:none;">Enviar cadastro</button>
                </div>
            </form>

            <div class="trust">
                <span class="trust-dot"></span>
                Seus dados são enviados com segurança diretamente para o CRM de Agentes.
            </div>
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const agreementModal = document.getElementById('agreement-modal');
        const agreementActions = document.getElementById('agreement-actions');
        const agreeButton = document.getElementById('agree-button');
        const disagreeButton = document.getElementById('disagree-button');

        if (agreeButton && agreementModal) {
            agreeButton.addEventListener('click', function () {
                agreementModal.classList.add('is-hidden');
            });
        }

        if (disagreeButton && agreementActions) {
            disagreeButton.addEventListener('click', function () {
                agreementActions.innerHTML = '<p class="agreement-thank-you">OBRIGADO, BOA SORTE!</p>';
            });
        }

        const steps = Array.from(document.querySelectorAll('.form-step'));
        const stepChip = document.getElementById('step-chip');
        const progressValue = document.getElementById('progress-value');
        const nextButton = document.getElementById('next-button');
        const prevButton = document.getElementById('prev-button');
        const submitButton = document.getElementById('submit-button');
        let currentStep = 0;

        const applyPhoneMask = (value) => {
            const digits = value.replace(/\D/g, '').slice(0, 11);
            if (digits.length <= 2) return digits;
            if (digits.length <= 6) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
            if (digits.length <= 10) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
            return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
        };

        const phoneInput = document.getElementById('phone_number');
        if (phoneInput) {
            phoneInput.addEventListener('input', function () {
                this.value = applyPhoneMask(this.value);
            });

            if (phoneInput.value) {
                phoneInput.value = applyPhoneMask(phoneInput.value);
            }
        }

        const updateStep = () => {
            steps.forEach((step, index) => {
                step.classList.toggle('is-active', index === currentStep);
            });

            const stepNumber = currentStep + 1;
            const label = steps[currentStep].dataset.label;
            stepChip.textContent = `Etapa ${stepNumber} de ${steps.length} · ${label}`;
            progressValue.style.width = `${(stepNumber / steps.length) * 100}%`;

            prevButton.style.display = currentStep === 0 ? 'none' : 'inline-block';
            nextButton.style.display = currentStep === steps.length - 1 ? 'none' : 'inline-block';
            submitButton.style.display = currentStep === steps.length - 1 ? 'inline-block' : 'none';
        };

        const validateCurrentStep = () => {
            const activeFields = steps[currentStep].querySelectorAll('input, select');
            for (const field of activeFields) {
                if (!field.checkValidity()) {
                    field.reportValidity();
                    return false;
                }
            }
            return true;
        };

        nextButton.addEventListener('click', function () {
            if (!validateCurrentStep()) {
                return;
            }

            if (currentStep < steps.length - 1) {
                currentStep += 1;
                updateStep();
            }
        });

        prevButton.addEventListener('click', function () {
            if (currentStep > 0) {
                currentStep -= 1;
                updateStep();
            }
        });

        @if ($errors->has('name'))
            currentStep = 0;
        @elseif ($errors->has('phone_number'))
            currentStep = 1;
        @elseif ($errors->has('email'))
            currentStep = 2;
        @elseif ($errors->has('solar_experience'))
            currentStep = 3;
        @endif

        updateStep();
    });
</script>
</body>
</html>
