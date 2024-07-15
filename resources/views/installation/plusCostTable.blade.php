<div class="columns pt-6 p-2">
    <div class="column">
        <table class="table is-fullwidth">
            <thead>
            <tr>
                <th>Descrição do custo</th>
                <th>Valor do custo</th>
                <th>Comprovante pagamento do custo</th>
                <th>NF do custo</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse($plusCosts as $key => $plusCost)
                <tr>
                    <td>{{ $plusCost['description'] }}</td>
                    <td>R$ {{ $plusCost['value'] }}</td>
                    <td>
                        <a href="{{ $plusCost['proof_of_payment'] }}" class="button is-primary has-icon">
                            <ion-icon name="eye-outline"></ion-icon> &nbsp;&nbsp; Visualizar Comprovante
                        </a>
                    </td>
                    <td>
                        <a href="{{ $plusCost['invoice'] }}" class="button is-primary has-icon">
                            <ion-icon name="eye-outline"></ion-icon> &nbsp;&nbsp; Visualizar NF
                        </a>
                    </td>
                    <td>
                        <a class="button is-danger"
                           onclick="return confirm('deseja realmente excluir o custo adicional?')"
                           href="{{ route('installation.deletePlusCost', [$key]) }}">
                            <ion-icon name="trash-outline" class="table-icon"></ion-icon>
                        </a>
                    </td>
                </tr>
            @empty
                <tr class="is-fullwidth">
                    <td> Não há custos adicionais cadastrados.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        <hr>

        <h4 class="pt-3"><span class="has-text-weight-bold">SOMA DOS CUSTOS ADICIONAIS:</span> ------------
            R$ {{ $costSums['plusCosts'] ? floatToMoney($costSums['plusCosts']) : '0,00' }}</h4><br>
        <h4><span class="has-text-weight-bold">CUSTO MATERIAL C.A:</span> ----------------------
            R$ {{ $installation->ca_cost ? floatToMoney($installation->ca_cost) : '0,00' }}</h4><br>
        <h4><span class="has-text-weight-bold">MÃO-DE-OBRA INSTALAÇÃO:</span> ---------------
            R$ {{ $installation->installation_cost ? floatToMoney($installation->installation_cost) : '0,00' }}</h4><br>

        @php
         $marginColor = $costSums['previousMargin'] > $costSums['totalCost'] ? 'is-success' : 'is-danger';
         $marginValue = $costSums['previousMargin'] - $costSums['totalCost'];
        @endphp

        <span class="tag is-justify-content-left is-warning is-large w-50"><span class="has-text-weight-bold">MARGEM ESTIPULADA: &nbsp; </span> ------------------------ R$ {{ $costSums['previousMargin'] ? floatToMoney($costSums['previousMargin']) : '0,00' }}</span><br>
        <span class="tag is-justify-content-left is-warning is-large w-50"><span class="has-text-weight-bold">TOTAL DA OBRA: &nbsp; </span> ----------------------------- R$ {{ $costSums['totalCost'] ? floatToMoney($costSums['totalCost']) : '0,00' }}</span><br>
        <span class="tag is-justify-content-left {{ $marginColor }} is-large w-50"><span class="has-text-weight-bold">MARGEM FINAL: &nbsp; </span> ------------------------------ R$ {{ $costSums ? floatToMoney($marginValue) : '0,00' }}</span>

    </div>
</div>
