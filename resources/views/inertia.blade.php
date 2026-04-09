<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @php
        $manifestPath = public_path('mix-manifest.json');
        $manifest = file_exists($manifestPath) ? file_get_contents($manifestPath) : '';
        $hasInertiaEntrypoint = str_contains($manifest, '"/js/inertia.js"');
    @endphp

    @if ($hasInertiaEntrypoint)
        <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
        <script src="{{ mix('/js/inertia.js') }}" defer></script>
        @inertiaHead
    @endif
</head>
<body style="margin: 0;">
@if ($hasInertiaEntrypoint)
    @inertia
@else
    <main style="min-height: 100vh; display: grid; place-items: center; background: #ffffff; color: #111827; font-family: Inter, Arial, sans-serif; padding: 24px;">
        <section style="max-width: 720px; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; box-shadow: 0 8px 20px rgba(17,24,39,0.06);">
            <h1 style="margin-top: 0; font-size: 1.5rem;">Entrypoint do Inertia ainda não foi gerado.</h1>
            <p style="line-height: 1.5; margin-bottom: 12px;">Não encontramos <code>/js/inertia.js</code> no arquivo <code>public/mix-manifest.json</code>.</p>
            <p style="line-height: 1.5; margin-bottom: 0;">Gere os assets e recarregue a página: <code>npm install</code> e depois <code>npm run dev</code>.</p>
        </section>
    </main>
@endif
</body>
</html>
