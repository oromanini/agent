<form action="{{ route('installation.updatePictures', [$installation->id]) }}"
      enctype="multipart/form-data"
      method="post">
    @csrf
    <div class="columns">
        <div class="column">
            <h2 class="title has-icon is-3">
                <ion-icon name="camera-outline"></ion-icon>&nbsp;Fotos da instalação
            </h2>
        </div>
    </div>

    @php
        $pictures = [
            'panels' =>             ['description' => 'painéis', 'file' => jsonToArray($installation->installation_images)['panels'] ?? null],
            'general' =>            ['description' => "Inversor, DPS's e Str.Box", 'file' => jsonToArray($installation->installation_images)['general'] ?? null],
            'inverterTag' =>        ['description' => 'Etiqueta do inversor', 'file' => jsonToArray($installation->installation_images)['inverterTag'] ?? null],
            'datalogger' =>         ['description' => 'Datalogger', 'file' => jsonToArray($installation->installation_images)['datalogger'] ?? null],
            'patternWithPlate' =>   ['description' => 'Padrão c/placa de geração', 'file' => jsonToArray($installation->installation_images)['patternWithPlate'] ?? null],
            'ca_tension' =>         ['description' => 'Tensão C.A', 'file' => jsonToArray($installation->installation_images)['ca_tension'] ?? null],
            'cc_tension' =>         ['description' => 'Tensão C.C', 'file' => jsonToArray($installation->installation_images)['cc_tension'] ?? null],
            'ca_current' =>         ['description' => 'Corrente C.A', 'file' => jsonToArray($installation->installation_images)['ca_current'] ?? null],
            'cc_current' =>         ['description' => 'Corrente C.C', 'file' => jsonToArray($installation->installation_images)['cc_current'] ?? null],
            'grounding' =>          ['description' => 'Aterramento', 'file' => jsonToArray($installation->installation_images)['grounding'] ?? null],
            'dps' =>                ['description' => "DPS's", 'file' => jsonToArray($installation->installation_images)['dps'] ?? null]
        ];
    @endphp
    <div class="columns is-fullwidth is-flex-wrap-wrap">
        @foreach($pictures as $key => $picture)
            <div class="column is-3">
                <div id="{{ $key }}" class="file is-fullwidth is-centered is-boxed is-success has-name">
                    <label class="file-label" style="text-align: center">
                        <input class="file-input" {{ $key == 'panels' ? 'multiple' : '' }} type="file"
                               name="{{ $key }}">
                        <span class="file-cta">
                    <ion-icon name="image-outline"></ion-icon>
                    <span class="file-label text-center">Foto {{ $picture['description'] }}</span>
                </span>
                        <span class="file-name">
                    Arquivo não selecionado
                </span>
                    </label>
                </div>
            </div>
        @endforeach
    </div>
    <hr>
    <div class="columns is-justify-content-center">
        <button class="button is-large is-primary has-icon" type="submit">
            <span class="icon"><ion-icon name="reload-outline"></ion-icon></span>
            &nbsp;&nbsp; Atualizar fotos
        </button>
    </div>
    <hr>
    <div class="columns is-fullwidth is-flex-wrap-wrap">

        @if(is_null($installation->installation_images))
            <h1 class="title is-3 column is-12 is-flex is-justify-content-center">
                <ion-icon name="close-circle-outline"></ion-icon>&nbsp;Nenhuma imagem foi anexada.
            </h1>
        @else
            @foreach($pictures as $key => $picture)
                @if($key == 'panels')
                    @foreach($picture['file'] as $file)
                        <div class="column is-3" style="padding: 50px">
                            <div class="card">
                                <div class="card-image">
                                    <figure class="image is-4by3">
                                        <img class="cardimage" onerror="this.src='/img/no-image.png';"
                                             src="{{ '/' . str_replace('public', 'storage', $file) }}"
                                             alt="Placeholder image">
                                    </figure>
                                </div>
                                <div class="card-content">
                                    <div class="subtitle">
                                        {{ $picture['description'] }}
                                    </div>
                                </div>
                                <footer class="card-footer">
                                    <a href="{{ '/' . str_replace('public', 'storage', $file) }}"
                                       target="_blank"
                                       class="card-footer-item">Abrir</a>
                                    <a href="#" class="card-footer-item">Excluir</a>
                                </footer>
                            </div>
                        </div>
                    @endforeach
                @else
                    @if(!is_null($picture['file']))
                        <div class="column is-3" style="padding: 50px">
                            <div class="card">
                                <div class="card-image">
                                    <figure class="image is-4by3">
                                        <img class="cardimage" onerror="this.src='/img/no-image.png';"
                                             src="{{ '/' . str_replace('public', 'storage', $picture['file']) }}"
                                             alt="Placeholder image">
                                    </figure>
                                </div>
                                <div class="card-content">
                                    <div class="subtitle">
                                        {{ $picture['description'] }}
                                    </div>
                                </div>
                                <footer class="card-footer">
                                    <a href="{{ '/' . str_replace('public', 'storage', $picture['file']) }}"
                                       target="_blank"
                                       class="card-footer-item">Abrir</a>
                                    <a href="#" class="card-footer-item">Excluir</a>
                                </footer>
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach
        @endif
    </div>
</form>
@include('installation.tabs.images_script')
