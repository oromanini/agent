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

            <div class="notification is-primary is-light editor-tips">
                <p><strong>Comece por aqui (rápido):</strong></p>
                <ol>
                    <li>Clique em <strong>Upload de imagem</strong> para enviar sua arte.</li>
                    <li>Selecione um bloco no canvas e clique em <strong>Aplicar imagem como fundo</strong>.</li>
                    <li>Use os botões <strong>Adicionar texto</strong> e <strong>Adicionar imagem</strong> para montar o layout.</li>
                </ol>
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
                        <div class="editor-quick-actions">
                            <button type="button" class="button is-small is-primary" data-action="add-text">Adicionar texto</button>
                            <button type="button" class="button is-small is-link" data-action="add-image">Adicionar imagem</button>
                            <button type="button" class="button is-small is-info" data-action="add-background">Seção com fundo</button>
                            <button type="button" class="button is-small is-warning" data-action="open-assets">Upload de imagem</button>
                            <button type="button" class="button is-small is-success" data-action="apply-bg">Aplicar imagem como fundo</button>
                        </div>
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
    <style>
        .editor-tips ol {
            margin: 8px 0 0 20px;
        }

        .editor-quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
        }

        #gjs {
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
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
                    label: 'Estrutura',
                    command: 'sw-visibility',
                    attributes: { title: 'Mostrar estrutura' },
                },
                {
                    id: 'open-sm',
                    label: 'Estilos',
                    command: 'open-sm',
                    attributes: { title: 'Estilos' },
                },
                {
                    id: 'open-blocks',
                    label: 'Blocos',
                    command: 'open-blocks',
                    attributes: { title: 'Blocos' },
                },
                {
                    id: 'open-assets',
                    label: 'Imagens',
                    command: 'open-assets',
                    attributes: { title: 'Biblioteca de imagens' },
                },
            ],
        });

        function addBlock(blockId) {
            const block = editor.BlockManager.get(blockId);
            if (!block) return;

            editor.addComponents(block.get('content'));
        }

        function applyImageAsBackground() {
            const selected = editor.getSelected();
            if (!selected) {
                alert('Selecione um bloco no layout para aplicar o fundo.');
                return;
            }

            editor.AssetManager.open({
                select(asset) {
                    const source = typeof asset === 'string' ? asset : (asset.get ? asset.get('src') : asset.src);
                    if (!source) return;

                    selected.addStyle({
                        'background-image': `url(${source})`,
                        'background-size': 'cover',
                        'background-position': 'center center',
                        'background-repeat': 'no-repeat',
                    });

                    editor.AssetManager.close();
                },
            });
        }

        document.querySelectorAll('[data-action]').forEach((button) => {
            button.addEventListener('click', () => {
                const action = button.dataset.action;

                if (action === 'add-text') addBlock('text');
                if (action === 'add-image') addBlock('image');
                if (action === 'add-background') addBlock('background-cover');
                if (action === 'open-assets') editor.runCommand('open-assets');
                if (action === 'apply-bg') applyImageAsBackground();
            });
        });

        editor.setComponents(htmlField.value || '');
        editor.setStyle(cssField.value || '');

        document.querySelector('form').addEventListener('submit', () => {
            htmlField.value = editor.getHtml();
            cssField.value = editor.getCss();
        });
    </script>
@endpush
