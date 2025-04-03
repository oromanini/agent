<div class="page page-break" style="background-image: url({{public_path('/img/proposal_v2/4.jpg')}})">
    @include('proposals.pdf_v2.number')

    <div id="table">
        <table style="text-align: center">
            <thead>
            <tr>
                <th id="th-pb">Ano</th>
                <th id="th-pb">Geração</th>
                <th id="th-pb">Degradação</th>
                <th id="th-pb">Valor kWh</th>
                <th id="th-pb">Inflação</th>
                <th id="th-pb">Consumo</th>
                <th id="th-pb">Economia</th>
                <th id="th-pb">Saldo</th>
            </tr>
            </thead>
            <tbody>
            @foreach($payback['data'] as $key => $value)
                <tr>
                    <td id="td-pb" style="color:#6b7280;">{{ $key }}</td>
                    <td id="td-pb" class="@if($value['balance']>1) is-green @else is-red @endif" @if(strlen(ceil($value['generation'])) >= 5) style="font-size: 8pt !important;" @endif>{{ceil($value['generation'])}} <span style="font-size: 6pt">kwh/mês</span></td>
                    <td id="td-pb" class="@if($value['balance']>1) is-green @else is-red @endif">0,7%</td>
                    <td id="td-pb" class="@if($value['balance']>1) is-green @else is-red @endif"><span style="font-size: 6pt">R$</span> {{floatToMoney($value['kw_value'])}}</td>
                    <td id="td-pb" class="@if($value['balance']>1) is-green @else is-red @endif">3%</td>
                    <td id="td-pb" class="@if($value['balance']>1) is-green @else is-red @endif">{{ ceil($proposal->average_consumption) }} <span style="font-size: 6pt">kWh/mês</span></td>
                    <td id="td-pb" class="@if($value['balance']>1) is-green @else is-red @endif"><span style="font-size: 6pt">R$</span> {{ floatToMoney($value['economy']) }}</td>
                    <td id="td-pb" id="balance" class="@if($value['balance']>1) is-green @else is-red @endif"><span style="font-size: 6pt">R$</span> {{ floatToMoney($value['balance']) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>

    table {
        border-collapse: collapse;
        border: 2px solid #ffb900;
    }

    #td-pb {
        border-collapse: collapse;
        border: 2px solid #ffb900;
        padding: 8px 20px;
        font-size: 10pt;
    }
    #th-pb {
        padding: 5px 20px;
        font-size: 10pt;
    }

    #table {
        position: absolute;
        top: 770px;
        left: 60px;
    }

    .is-red {
        color: #ff4d00 !important;
    }

    .is-green {
        color: #06784a !important;
    }

    th {
        font-size: 8pt !important;
    }

    #balance {
        width: 300px !important;
    }

</style>
