<div class="columns" style="margin-top: 60px;">
    <div class="column is-3">
        <label for=""><ion-icon name="person-outline"></ion-icon> Cliente</label>
        <p class="proposalData"><a href="{{ route('client.edit', [$proposal->client->id]) }}">{{ $proposal->client->name }}</a></p>
    </div>
    <div class="column is-3">
        <label for=""><ion-icon name="accessibility-outline"></ion-icon> Agente</label>
        <p class="proposalData">{{ $proposal->agent->name }} <a style="color: #50C55E" href="https://api.whatsapp.com/send?phone=55{{ filter_var($proposal->agent->phone_number, FILTER_SANITIZE_NUMBER_INT) }}"><ion-icon name="logo-whatsapp"></ion-icon></a></p>
    </div>
    <div class="column is-3">
        <label for=""><ion-icon name="call-outline"></ion-icon> Telefone cliente</label>
        <p class="proposalData">{{ $proposal->client->phone_number }}</p>
    </div>
    <div class="column is-3">
        <label for=""><ion-icon name="mail-outline"></ion-icon> Email agente</label>
        <p class="proposalData">{{ $proposal->agent->email }}</p>
    </div>
</div>
<div class="columns">
    <div class="column is-3">
        <label for=""><ion-icon name="business-outline"></ion-icon> Cidade</label>
        <p class="proposalData">{{ $proposal->client->addresses->first()->city->name_and_federal_unit }}</p>
    </div>
    <div class="column is-3">
        <label for=""><ion-icon name="flash-outline"></ion-icon> Documento</label>
        <p class="proposalData">{{ $proposal->client->document }}</p>
    </div>
    <div class="column is-3">
        <label for=""><ion-icon name="flash-outline"></ion-icon> CEP</label>
        <p class="proposalData">{{ $proposal->client->addresses->first()->zipcode }}</p>
    </div>
    <div class="column is-3">
        <label for=""><ion-icon name="sunny-outline"></ion-icon> Incidência Solar</label>
        <p class="proposalData">{{ $proposal->client->addresses->first()->city->incidence() }}</p>
    </div>
</div>
