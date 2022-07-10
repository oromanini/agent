@if(!empty(jsonToArray($proposal->preInspection->roof)))
    @foreach(jsonToArray($proposal->preInspection->roof) as $roof)
        <div class="column is-3" style="padding: 50px">

            <div class="card">
                <div class="card-image">
                    <figure class="image is-4by3">
                        <img class="cardimage" onerror="this.src='/img/no-image.png';"
                             src="/storage/{{ str_replace('public/', '', $roof) }}" alt="Placeholder image">
                    </figure>
                </div>
                <div class="card-content">
                    <div class="title">
                        Telhado
                    </div>
                </div>
                <footer class="card-footer">
                    <a href="/storage/{{str_replace('public/', '', $roof)}}" target="_blank" class="card-footer-item">Abrir</a>
                    <a href="#" class="card-footer-item">Excluir</a>
                </footer>
            </div>
        </div>
    @endforeach
@else
    <div>
        <h1 class="title is-1 ml-2">O agente de negócios não anexou as imagens</h1>
    </div>
@endif

@php

    $preInspection = $proposal->preInspection;
    $images = [
        1 => ['name' => 'Croqui', 'file' => isset($preInspection->croqui) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->croqui)[0]) ? '/storage/' . jsonToArray($preInspection->croqui)[0] : json_encode([])],
        2 => ['name' => 'Padrão', 'file' => isset($preInspection->pattern) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->pattern)[0]) ? '/storage/' . jsonToArray($preInspection->pattern)[0] : json_encode([])],
        3 => ['name' => 'Disjuntor', 'file' => isset($preInspection->circuit_breaker) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->circuit_breaker)[0]) ? '/storage/' . jsonToArray($preInspection->circuit_breaker)[0] : json_encode([])],
        4 => ['name' => 'Quadro de distrib.', 'file' => isset($preInspection->switchboard) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->switchboard)[0]) ? '/storage/' . jsonToArray($preInspection->switchboard)[0] : json_encode([])],
        5 => ['name' => 'Poste', 'file' => isset($preInspection->post) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->post)[0]) ? '/storage/' . jsonToArray($preInspection->post)[0] : json_encode([])],
        7 => ['name' => 'Bússola', 'file' => isset($preInspection->compass) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->compass)[0]) ? '/storage/' . jsonToArray($preInspection->compass)[0] : json_encode([])],

    ];

@endphp

@forelse($images as $image)

    @if(!empty(jsonToArray($image['file'])))
        <div class="column is-3" style="padding: 50px">
            <div class="card">
                <div class="card-image">
                    <figure class="image is-4by3">
                        <img class="cardimage" onerror="this.src='/img/no-image.png';"
                             src="{{ str_replace('public/', '', $image['url']) }}" alt="Placeholder image">
                    </figure>
                </div>
                <div class="card-content">
                    <div class="title">
                        {{ $image['name'] }}
                    </div>
                </div>
                <footer class="card-footer">
                    <a href="{{ str_replace('public/', '', $image['url']) }}" target="_blank" class="card-footer-item">Abrir</a>
                    <a href="#" class="card-footer-item">Excluir</a>
                </footer>
            </div>

        </div>
    @endif

@empty
    <h1>O agente de negócios não anexou as imagens</h1>
@endforelse

