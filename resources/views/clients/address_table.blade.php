<div class="box">
    <div class="columns" style="margin: 10px">
        <div class="column is-10"><h4 class="title is-4">Endereços do cliente</h4></div>
        @if(!is_null($addresses->first()->consumer_unit_id))
            <div class="column is-2">
                <button class="address-modal-trigger button is-warning"
                        data-target="address_modal">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    Novo endereço
                </button>
            </div>
        @endif
    </div>
    <div class="columns">
        <div class="column">
            <table class="table is-fullwidth">
                <thead>
                <tr>
                    <th><abbr title="Position">#ID</abbr></th>
                    <th>Rua</th>
                    <th>Número</th>
                    <th>Cidade/Estado</th>
                    <th>U.C</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @forelse($addresses as $address)
                    <tr>
                        <td>{{ $address->id }}</td>
                        <td>{{ $address->street }}</td>
                        <td>{{ $address->number }}</td>
                        <td>{{ $address->city->name_and_federal_unit }}</td>
                        <td>
                            @if(!is_null($address->consumer_unit_id))
                                <a href="/storage/{{ str_replace('public/', '', $address->consumerUnit->eletricity_bill) }}"
                                   class="button is-primary" target="_blank">
                                    <ion-icon name="eye-outline"></ion-icon>
                                    Visualizar U.C</a>
                            @else
                                <button class="address-modal-trigger button is-warning"
                                        data-target="address_modal">
                                    <ion-icon name="add-circle-outline"></ion-icon>
                                    Cadastrar U.C
                                </button>
                            @endif
                        </td>
                        <td>
                            <a @if(count($addresses) == 1) disabled onclick="event.preventDefault()"
                               @endif class="button is-danger">
                                <ion-icon style="padding: 0;" name="trash-bin-outline"></ion-icon>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>Não há endereços cadastrados</tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
