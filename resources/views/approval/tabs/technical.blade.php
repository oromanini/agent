<div class="columns" style="padding: 10px 10px">
    <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Vistoria</h3>
</div>
<br>

<form action="{{ route('approval.update.inspection', [$proposal->id]) }}" method="post" enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <div class="columns is-justify-content-left is-flex-wrap-wrap">
        <div class="column mr-3">
            <div class="field">
                <label for="status" class="label">Status</label>
                <div class="select is-multiline is-rounded  @error('status') is-danger @enderror">
                    <select id="status" name="status_id">
                        @foreach($inspectionStatuses as $status)
                            <option
                                value="{{ $status->id }}" {{ !is_null($inspection) && $inspection->status->id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('status')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="column">
            <label class="label" for="tension_pattern">Tensão</label>
            <p>{{ $proposal->tension_pattern }}</p>
        </div>

        <div class="column">
            <label class="label" for="circuit_breaker">Disjuntor (A)</label>
            <p>{{ $proposal->preInspection->circuit_breaker_amperage ?? 'Não informado' }}</p>
        </div>

        <div class="column">
            <label class="label" for="owner_document">Documento do cliente</label>
            @if(isset($proposal->client->owner_document))
                <a href="/storage/{{ str_replace('public/', '', $proposal->client->owner_document) }}"
                   class="button is-danger" target="_blank">
                    <ion-icon name="eye-outline"></ion-icon>
                    Visualizar Documento</a>
            @else
                <p>Não anexado</p>
            @endif
        </div>

        <div class="column">
            <label class="label" for="owner_document">N° U.C de instalação</label>
            @if(!is_null($proposal->client->addresses->first()->consumerUnit))
                <p> {{$proposal->client->addresses->first()->consumerUnit->number}} </p>
            @else
                <p>U.C não cadastrada!</p>
            @endif
        </div>

        <div class="column">
            <label class="label" for="owner_document">U.C de instalação</label>
            @if(!is_null($proposal->client->addresses->first()->consumerUnit))
                <a href="/storage/{{ str_replace('public/', '', $proposal->client->addresses->first()->consumerUnit->eletricity_bill) }}"
                   class="button is-primary" target="_blank">
                    <ion-icon name="eye-outline"></ion-icon>
                    Visualizar U.C</a>
            @else
                <p>U.C não cadastrada!</p>
            @endif
        </div>

        <div class="column">
            <label class="label" for="owner_document">U.C's Cadastradas</label>
            <p>{{ count($proposal->client->addresses) }}
                @if(count($proposal->client->addresses) > 1)
                    - <a class="is-link" href="{{ route('client.edit', [$proposal->client->id]) }}">(Ver UC's)</a>
                @endif
            </p>
        </div>
    </div>

    <div class="columns">
        <div class="column">
            <label class="label" for="observations">Observações do agente</label>
            <p>{{ $proposal->preInspection->observations ?? 'Sem observações' }}</p>
        </div>
    </div>

    <hr>
    <div class="columns">
        <div class="column is-3">
            <label for="three_dimensional" class="label">
                <span class="icon"><ion-icon name="image-outline"></ion-icon></span>
                3D dos módulos</label>
            @if(isset($proposal->inspection->three_dimensional))
                <a href="/storage/{{ str_replace('public/', '', $proposal->inspection->three_dimensional) }}"
                   class="button is-danger" target="_blank">
                    <ion-icon name="eye-outline"></ion-icon>
                    Visualizar 3D</a>
            @else
                <div class="file has-name">
                    <label class="file-label">
                        <input class="file-input" type="file" name="three_dimensional" id="three_dimensional">
                        <span class="file-cta">
                                  <span class="file-icon">
                                    <ion-icon name="folder-outline"></ion-icon>
                                  </span>
                                  <span class="file-label">
                                    Escolher arquivo…
                                  </span>
                                </span>
                        <span class="file-name">    
                                    Nenhum arquivo selecionado
                                </span>
                    </label>
                </div>
            @endif
        </div>
    </div>
    <hr>
    <div class="columns">
        <div class="column">
            <label class="label" for="observations">Observações da vistoria e/ou medidas necessárias</label>
            <textarea id="note" name="note" class="textarea"
                      placeholder="Adequação necessária, observações, etc...">{{ isset($inspection) ? $inspection->note : ''  }}</textarea>
        </div>
    </div>

    <div class="columns">
        <div class="column is-flex is-justify-content-center">
            <button type="submit" class="button is-large is-info">
                <ion-icon name="save-outline"></ion-icon> &nbsp; Salvar
            </button>
        </div>
    </div>
</form>

<hr>

<hr>
<div class="columns is-flex is-flex-wrap-wrap" style="margin-top: 50px">
    @include('pre_inspection.images')
</div>

@include('pre_inspection.images_script')

<style>
    .pre-inspection-video {
        background-color: #a00000;
        border-radius: 50px;
        color: #fff;
        padding: 5px 10px;
    }

    .oranged {
        color: #ff6200;
        font-weight: 800;
    }

</style>
