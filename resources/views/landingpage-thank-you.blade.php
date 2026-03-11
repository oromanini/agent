<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obrigado pelo cadastro</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #7c2d12, #b45309);
            color: #fff;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            box-sizing: border-box;
        }

        .container {
            width: 100%;
            max-width: 900px;
            background: #fff;
            color: #0f172a;
            border-radius: 16px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35);
            padding: 32px;
            box-sizing: border-box;
        }

        h1 {
            margin-top: 0;
            color: #c2410c;
        }

        p {
            margin-bottom: 20px;
        }

        .video-wrapper {
            position: relative;
            width: 100%;
            padding-top: 56.25%;
            border-radius: 12px;
            overflow: hidden;
        }

        .video-wrapper iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Obrigado pelo seu cadastro!</h1>
    <p>Seu envio foi recebido com sucesso. Assista ao vídeo abaixo para os próximos passos:</p>

    <div class="video-wrapper">
        <iframe
            src="https://www.youtube.com/embed/yLSVl9S6loI"
            title="Vídeo de agradecimento"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen>
        </iframe>
    </div>
</div>
</body>
</html>
