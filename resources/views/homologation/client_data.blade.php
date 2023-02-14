<div class="columns" style="margin-top: 60px;">
    <div class="column is-3">
        <label for="">
            <ion-icon name="person-outline"></ion-icon>
            Cliente</label>
        <p class="proposalData"><a
                href="{{ route('client.edit', [$proposal->client->id]) }}">{{ $proposal->client->name }}</a></p>
    </div>
    <div class="column is-3">
        <label for="">
            <ion-icon name="accessibility-outline"></ion-icon>
            Agente</label>
        <p class="proposalData">{{ $proposal->agent->name }} <a style="color: #50C55E"
                                                                href="https://api.whatsapp.com/send?phone=55{{ filter_var($proposal->agent->phone_number, FILTER_SANITIZE_NUMBER_INT) }}">
                <ion-icon name="logo-whatsapp"></ion-icon>
            </a></p>
    </div>
    <div class="column is-3">
        <label for="">
            <ion-icon name="call-outline"></ion-icon>
            Telefone cliente</label>
        <p class="proposalData">{{ $proposal->client->phone_number }}</p>
    </div>
    <div class="column is-3">
        <label for="">
            <ion-icon name="mail-outline"></ion-icon>
            Email agente</label>
        <p class="proposalData">{{ $proposal->agent->email }}</p>
    </div>
</div>
<div class="columns">
    <div class="column is-3">
        <label for="">
            <ion-icon name="business-outline"></ion-icon>
            Cidade</label>
        <p class="proposalData">{{ $proposal->client->addresses->first()->city->name_and_federal_unit }}</p>
    </div>
    <div class="column is-3">
        <label for="">
            <ion-icon name="flash-outline"></ion-icon>
            Documento</label>
        <p class="proposalData">
            <a onclick="return window.event.preventDefault()"
               href="/storage/{{ str_replace('public/', '', $proposal->client->owner_document) }}"
               {{ !isset($proposal->client->owner_document) ? 'disabled onclick="return window.event.preventDefault()"' : '' }}
               class="button is-danger" target="_blank">
                <ion-icon name="eye-outline"></ion-icon>
                Visualizar Documento</a>
        </p>
    </div>
    <div class="column is-3">
        <label for="">
            <ion-icon name="flash-outline"></ion-icon>
            CEP</label>
        <p class="proposalData">{{ $proposal->client->addresses->first()->zipcode }}</p>
    </div>
    <div class="column is-3">
        <label for="">
            <ion-icon name="sunny-outline"></ion-icon>
            Incidência Solar</label>
        <p class="proposalData">{{ $proposal->client->addresses->first()->city->incidence() }}</p>
    </div>
</div>
<div class="columns">
    <div class="column is-3">
        <label for="">
            <ion-icon name="business-outline"></ion-icon>
            Tensão do cliente</label>
        <p class="proposalData">{{ $proposal->tension_pattern }}</p>
    </div>
    <div class="column is-3">
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
    <div class="column is-3">
        <label class="label" for="owner_document">U.C's Cadastradas</label>
        <p>{{ count($proposal->client->addresses) }}
            @if(count($proposal->client->addresses) > 1)
                - <a class="is-link" href="{{ route('client.edit', [$proposal->client->id]) }}">(Ver UC's)</a>
            @endif
        </p>
    </div>
    <div class="column is-3">
        <label for="">
            <ion-icon name="business-outline"></ion-icon>
            Data de entrada</label>
        <p class="proposalData">
            <span class="tag is-medium {{$deadlineColor}}">
                {{ $homologation->created_at->format('d/m/Y') . ' - ' . $deadline . ' Dias' }}
            </span>
        </p>
    </div>
</div>
