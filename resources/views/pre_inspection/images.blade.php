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
                    <div class="subtitle">
                        Telhado {{ $loop->index > 1 ? '(' . $loop->index . ')' : '' }}
                    </div>
                </div>
                <footer class="card-footer">
                    <a href="/storage/{{str_replace('public/', '', $roof)}}" target="_blank" class="card-footer-item">Abrir</a>
                    <a href="#" class="card-footer-item">Excluir</a>
                </footer>
            </div>
        </div>
    @endforeach
@endif

@php

    $preInspection = $proposal->preInspection;
    $images = [
        1 => ['name' => 'Croqui', 'file' => isset($preInspection->croqui) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->croqui)[0]) ? '/storage/' . jsonToArray($preInspection->croqui)[0] : json_encode([])],
        3 => ['name' => 'Estrutura', 'file' => isset($preInspection->roof_structure) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->roof_structure)[0]) ? '/storage/' . jsonToArray($preInspection->roof_structure)[0] : json_encode([])],
        4 => ['name' => 'Padrão', 'file' => isset($preInspection->pattern) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->pattern)[0]) ? '/storage/' . jsonToArray($preInspection->pattern)[0] : json_encode([])],
        5 => ['name' => 'Padrão (aberto)', 'file' => isset($preInspection->open_pattern) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->open_pattern)[0]) ? '/storage/' . jsonToArray($preInspection->open_pattern)[0] : json_encode([])],
        6 => ['name' => 'Disjuntor', 'file' => isset($preInspection->circuit_breaker) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->circuit_breaker)[0]) ? '/storage/' . jsonToArray($preInspection->circuit_breaker)[0] : json_encode([])],
        7 => ['name' => 'Quadro de distrib.', 'file' => isset($preInspection->switchboard) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->switchboard)[0]) ? '/storage/' . jsonToArray($preInspection->switchboard)[0] : json_encode([])],
        8 => ['name' => 'Poste', 'file' => isset($preInspection->post) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->post)[0]) ? '/storage/' . jsonToArray($preInspection->post)[0] : json_encode([])],
        9 => ['name' => 'Bússola', 'file' => isset($preInspection->compass) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->compass)[0]) ? '/storage/' . jsonToArray($preInspection->compass)[0] : json_encode([])],
        10 => ['name' => 'Faxada', 'file' => isset($preInspection->property_fax) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->property_fax)[0]) ? '/storage/' . jsonToArray($preInspection->property_fax)[0] : json_encode([])],
        11 => ['name' => 'Medidor', 'file' => isset($preInspection->meter) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->meter)[0]) ? '/storage/' . jsonToArray($preInspection->meter)[0] : json_encode([])],
        12 => ['name' => 'Local do inversor', 'file' => isset($preInspection->inverter_local) ?? json_encode([]), 'url' => isset(jsonToArray($preInspection->inverter_local)[0]) ? '/storage/' . jsonToArray($preInspection->inverter_local)[0] : json_encode([])],
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
                    <div class="subtitle">
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

