<div class="columns pt-6 p-2">
    @forelse($plusCosts as $key => $plusCost)
        <table class="table">
            <thead>
            <tr>
                <th>Descrição do custo</th>
                <th>Valor do custo</th>
                <th>Comprovante pagamento do custo</th>
                <th>NF do custo</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th><abbr title="Total">TOTAL: R$ {{ $installation->getPlusCostSum }}</abbr></th>
            </tr>
            </tfoot>
            <tbody>
            <tr>
                <td>{{ $plusCost->description }}</td>
                <td>{{ $plusCost->value }}</td>
                <td>{{ $plusCost->proof_of_payment }}</td>
                <td>{{ $plusCost->invoice }}</td>
                <td><a class="button is-warning" href="{{ route('installation.deletePlusCost', [$key]) }}"></a></td>
            </tr>
            </tbody>
        </table>
    @empty
        <div class="column box is-flex is-justify-content-center">
            Não há custos adicionais cadastrados.
        </div>
    @endforelse
</div>
