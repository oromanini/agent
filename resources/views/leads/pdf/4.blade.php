<div class="page page-break" style="background-image: url({{public_path('/img/leads/4.jpg')}})">
    <div id="investiment">
        - Investimento de <span id="pbinfo">R$ {{ floatToMoney($lead->pricing()['final_price']['finalPrice']) }}</span>;
    </div>
    <div id="paybackTime">
        - Payback (retorno) em <span id="pbinfo">{{$payback['years']}}</span>;
    </div>
    <div id="payback25years">
        - Retorno total de <span id="pbinfo">R$ {{ floatToMoney($payback['totalEconomy']) }}</span> em 25 anos;
    </div>
    <div id="profit">
        - Retorno total - investimento inicial = <span id="pbinfo">R$ {{ floatToMoney($payback['data'][25]['balance']) }}</span>;
    </div>

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
                    <td class="@if($value['balance']>1) is-green @else is-red @endif">{{ ceil($lead->average_consumption) }} <span style="font-size: 6pt">kWh/mês</span></td>
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
        color: #454444;
        font-size: 16pt;
        font-weight: 400;
    }

    #investiment {
        top: 400px;
        left: 60px;
    }

    #paybackTime {
        top: 500px;
        left: 60px;
    }

    #payback25years {
        top: 600px;
        left: 60px;
    }

    #profit {
        top: 700px;
        left: 60px;
    }

    table, th, td {
        border-collapse: collapse;
        border: 2px solid #454444;
        padding: 5px 22px;
        font-size: 10pt;
    }

    #tableTitle {
        font-size: 18pt;
        font-weight: 800;
        color: #B0921E;
        margin: 50px 0;
        text-align: center;
    }

    #pbinfo {
        font-size: 18pt;
        font-weight: 800;
        color: #B0921E;
        margin: 20px 0;
        text-align: center;
    }

    #table {
        position: absolute;
        top: 850px;
        left: 90px;
    }

    .is-red {
        color: #d00f0f !important;
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
