@extends('base')

@section('content')
    <section class="section">
        <div class="container">
            <h1 class="title">Editor de Template PDF (Propostas)</h1>
            <p class="subtitle">Acesso exclusivo para administradores.</p>

            <div class="notification is-info is-light">
                Variáveis disponíveis: <code>@{{proposal_id}}</code>, <code>@{{client_name}}</code>,
                <code>@{{client_city}}</code>, <code>@{{seller_name}}</code>,
                <code>@{{generated_at}}</code>
            </div>

            @php
                $defaultHtml = <<<'HTML'
<section class="pdf-page">
    <div class="hero-title">PROPOSTA #{{proposal_id}}</div>
    <div class="hero-content">
        <p><strong>Cliente:</strong> {{client_name}}</p>
        <p><strong>Cidade:</strong> {{client_city}}</p>
        <p><strong>Consultor:</strong> {{seller_name}}</p>
        <p><strong>Gerado em:</strong> {{generated_at}}</p>
    </div>
</section>
HTML;
            @endphp

            <form method="POST" action="{{ route('pdf-templates.update') }}">
                @csrf

                <div class="field">
                    <label class="label" for="name">Nome do template</label>
                    <div class="control">
                        <input id="name" name="name" class="input" value="{{ old('name', $template->name ?? 'Template Comercial V1') }}" required>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-9">
                        <div class="field">
                            <label class="label">Layout (Editor visual)</label>
                            <div id="gjs" style="border: 1px solid #ddd; min-height: 720px;"></div>
                            <textarea id="html" name="html" class="is-hidden">{{ old('html', $template->html ?? $defaultHtml) }}</textarea>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <label class="label">CSS</label>
                            <textarea id="css" name="css" class="textarea" rows="29">{{ old('css', $template->css ?? '') }}</textarea>
                        </div>
                        <button type="submit" class="button is-primary is-fullwidth">Salvar template</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
    <script src="https://unpkg.com/grapesjs"></script>
    <script>
        const htmlField = document.getElementById('html');
        const cssField = document.getElementById('css');

        const editor = grapesjs.init({
            container: '#gjs',
            fromElement: false,
            height: '720px',
            storageManager: false,
            panels: { defaults: [] },
            canvas: {
                styles: [
                    'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap',
                ],
            },
            assetManager: {
                upload: '{{ route('pdf-templates.upload-asset') }}',
                uploadName: 'file',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                autoAdd: true,
                assets: [],
            },
            blockManager: {
                appendTo: null,
                blocks: [
                    {
                        id: 'text',
                        label: 'Texto',
                        category: 'Básico',
                        content: '<p style="font-size:32px;color:#fff;font-family:Montserrat,sans-serif">Novo texto</p>',
                    },
                    {
                        id: 'image',
                        label: 'Imagem',
                        category: 'Mídia',
                        content: { type: 'image' },
                    },
                    {
                        id: 'background-cover',
                        label: 'Background',
                        category: 'Layout',
                        content: `
                            <section style="min-height:1120px;background-size:cover;background-position:center;padding:40px;">
                                <h2 style="color:white;font-size:48px;margin:0">Título</h2>
                            </section>
                        `,
                    },
                    {
                        id: 'qr-area',
                        label: 'Área QR',
                        category: 'Layout',
                        content: `
                            <div style="width:220px;height:220px;background:#fff;border-radius:24px;padding:16px;display:flex;align-items:center;justify-content:center;">
                                <span style="color:#555;font-size:18px;">QR Code</span>
                            </div>
                        `,
                    },
                ],
            },
            styleManager: {
                sectors: [
                    {
                        name: 'Geral',
                        open: true,
                        properties: ['display', 'position', 'top', 'right', 'left', 'bottom', 'width', 'height', 'padding', 'margin'],
                    },
                    {
                        name: 'Tipografia',
                        open: true,
                        properties: ['font-family', 'font-size', 'font-weight', 'color', 'line-height', 'text-align'],
                    },
                    {
                        name: 'Decoração',
                        open: false,
                        properties: ['background-color', 'background', 'border-radius', 'border', 'box-shadow', 'opacity'],
                    },
                ],
            },
        });

        editor.Panels.addPanel({
            id: 'basic-actions',
            buttons: [
                {
                    id: 'toggle-borders',
                    className: 'fa fa-clone',
                    command: 'sw-visibility',
                    attributes: { title: 'Mostrar estrutura' },
                },
                {
                    id: 'open-sm',
                    className: 'fa fa-paint-brush',
                    command: 'open-sm',
                    attributes: { title: 'Estilos' },
                },
                {
                    id: 'open-blocks',
                    className: 'fa fa-th-large',
                    command: 'open-blocks',
                    attributes: { title: 'Blocos' },
                },
            ],
        });

        editor.setComponents(htmlField.value || '');
        editor.setStyle(cssField.value || '');

        document.querySelector('form').addEventListener('submit', () => {
            htmlField.value = editor.getHtml();
            cssField.value = editor.getCss();
        });
    </script>
@endpush
