<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seja um Agente Alluz</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #7c2d12, #b45309);
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
            max-width: 900px;
            border-radius: 16px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35);
        }

        .hero {
            background: linear-gradient(160deg, #f59e0b, #f97316);
            color: #fff;
            padding: 40px;
        }

        .hero h1 {
            margin-top: 0;
            font-size: 2rem;
            line-height: 1.2;
        }

        .hero p {
            opacity: 0.95;
            line-height: 1.5;
        }

        .form-area {
            padding: 40px;
        }

        .form-area h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            margin-top: 14px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            box-sizing: border-box;
        }

        .btn {
            margin-top: 20px;
            width: 100%;
            border: 0;
            border-radius: 10px;
            padding: 13px;
            font-weight: bold;
            cursor: pointer;
            background: linear-gradient(160deg, #f59e0b, #ea580c);
            color: #fff;
        }

        .error {
            color: #b91c1c;
            font-size: 0.875rem;
            margin-top: 4px;
        }

        @media (max-width: 760px) {
            .card {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <section class="hero">
            <h1>Transforme sua carreira de energia solar</h1>
            <p>
                Preencha seus dados para entrar no time de agentes da Alluz Energia.
                Seu cadastro vai direto para o CRM de Agentes para atendimento rápido.
            </p>
        </section>

        <section class="form-area">
            <h2>Cadastre-se</h2>


            <form method="POST" action="{{ route('landingpage.store') }}">
                @csrf

                <label for="name">Nome completo</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror

                <label for="email">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror

                <label for="phone_number">Telefone (WhatsApp)</label>
                <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number') }}" required>
                @error('phone_number') <div class="error">{{ $message }}</div> @enderror

                <button type="submit" class="btn">Quero ser agente</button>
            </form>
        </section>
    </div>
</div>
</body>
</html>
