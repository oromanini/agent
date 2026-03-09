@extends('base')

@section('content')
    <section class="section">
        <div class="container">
            <h1 class="title">Editor de Template PDF (Propostas)</h1>
            <p class="subtitle">Acesso exclusivo para administradores.</p>

            <div class="notification is-info is-light">
                Variáveis disponíveis: <code>{{ '{{proposal_id}}' }}</code>, <code>{{ '{{client_name}}' }}</code>,
                <code>{{ '{{client_city}}' }}</code>, <code>{{ '{{seller_name}}' }}</code>,
                <code>{{ '{{generated_at}}' }}</code>
            </div>

            @php
                $defaultHtml = <<<'HTML'
<section style="padding:48px;font-family:Arial"><h1>Proposta #{{proposal_id}}</h1><p>Cliente: {{client_name}}</p><p>Cidade: {{client_city}}</p><p>Consultor: {{seller_name}}</p><p>Gerado em: {{generated_at}}</p></section>
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
                            <label class="label">Layout (HTML)</label>
                            <div id="gjs" style="border: 1px solid #ddd; min-height: 680px;"></div>
                            <textarea id="html" name="html" class="is-hidden">{{ old('html', $template->html ?? $defaultHtml) }}</textarea>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <label class="label">CSS</label>
                            <textarea id="css" name="css" class="textarea" rows="26">{{ old('css', $template->css ?? '') }}</textarea>
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
            height: '680px',
            storageManager: false,
            panels: { defaults: [] },
        });

        editor.setComponents(htmlField.value || '');
        editor.setStyle(cssField.value || '');

        document.querySelector('form').addEventListener('submit', () => {
            htmlField.value = editor.getHtml();
            cssField.value = editor.getCss();
        });
    </script>
@endpush
