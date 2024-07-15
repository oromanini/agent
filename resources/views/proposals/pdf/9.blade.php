<div class="page page-break" style="background-image: url({{public_path('/img/proposal/9.jpg')}})">
    <div id="investiment">R$ {{ floatToMoney($proposal->valueHistory->final_price) }}</div>
    <div id="paybackTime">{{$payback['years']}}</div>
    <div id="payback25years">R$ {{ floatToMoney($payback['totalEconomy']) }} </div>
    <div id="profit">R$ {{ floatToMoney($payback['data'][25]['balance']) }}</div>

    <div id="table">
        <div id="tableTitle">Payback/ Fluxo de economia</div>
        <table STYLE="text-align: center">
            <thead>
            <tr>
                <th>Ano</th>
                <th>Geração</th>
                <th>Degradação</th>
                <th>Valor kWh</th>
                <th>Inflação</th>
                <th>Consumo</th>
                <th>Economia</th>
                <th>Saldo</th>
            </tr>
            </thead>
            <tbody>
            @foreach($payback['data'] as $key => $value)
                <tr>
                    <td style="color:#6b7280;">{{ $key }}</td>
                    <td class="@if($value['balance']>1) is-green @else is-red @endif" @if(strlen(ceil($value['generation'])) >= 5) style="font-size: 8pt !important;" @endif>{{ceil($value['generation'])}} <span style="font-size: 6pt">kwh/mês</span></td>
                    <td class="@if($value['balance']>1) is-green @else is-red @endif">0,7%</td>
                    <td class="@if($value['balance']>1) is-green @else is-red @endif"><span style="font-size: 6pt">R$</span> {{floatToMoney($value['kw_value'])}}</td>
                    <td class="@if($value['balance']>1) is-green @else is-red @endif">3%</td>
                    <td class="@if($value['balance']>1) is-green @else is-red @endif">{{ ceil($proposal->average_consumption) }} <span style="font-size: 6pt">kWh/mês</span></td>
                    <td class="@if($value['balance']>1) is-green @else is-red @endif"><span style="font-size: 6pt">R$</span> {{ floatToMoney($value['economy']) }}</td>
                    <td id="balance" class="@if($value['balance']>1) is-green @else is-red @endif"><span style="font-size: 6pt">R$</span> {{ floatToMoney($value['balance']) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>

    #investiment, #paybackTime, #payback25years, #profit {
        position: absolute;
        color: #F9880D;
        font-size: 20pt;
        font-weight: 700;
    }

    #investiment {
        top: 530px;
        left: 60px;
    }

    #paybackTime {
        top: 530px;
        left: 900px;
    }

    #payback25years {
        top: 710px;
        left: 60px;
    }

    #profit {
        top: 710px;
        left: 900px;
    }

    table, th, td {
        border-collapse: collapse;
        border: 4px solid #F9880D;
        padding: 5px 22px;
        font-size: 10pt;
    }

    #tableTitle {
        font-size: 18pt;
        font-weight: 800;
        color: #F9880D;
        margin: 20px 0;
        text-align: center;
    }

    #table {
        position: absolute;
        top: 930px;
        left: 30px;
    }

    .is-red {
        color: #F9880D !important;
    }

    .is-green {
        color: #008852 !important;
    }

    th {
        font-size: 8pt !important;
    }

    #balance {
        width: 300px !important;
    }

</style>
