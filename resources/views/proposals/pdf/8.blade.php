<div class="page page-break" style="background-image: url({{public_path('/img/proposal/8.jpg')}})">
    <div id="systemPower">{{ $proposal->kwp }} kWp</div>
    <div id="estimatedGeneration">{{ ceil($proposal->estimated_generation) }} kWh/mês <span class="minifiedText">(estimado)</span>
    </div>

    <div class="table">
        <div id="tableTitle">Tabela de geração</div>
        <table STYLE="text-align: center">
            <thead>
            <tr>
                <th class="th">Mês</th>
                <th class="th">Consumo</th>
                <th class="th">Geração</th>
                <th class="th">Sobra</th>
            </tr>
            </thead>
            <tbody>
            @foreach($generationData['months'] as $key => $value)
                <tr>
                    <td class="td @if($value['excedente'] > 0) is-green @else is-red @endif">{{$key}}</td>
                    <td class="td @if($value['excedente'] > 0) is-green @else is-red @endif">{{$value['consumo']}}
                        kWh/mês
                    </td>
                    <td class="td @if($value['excedente'] > 0) is-green @else is-red @endif">{{$value['geracao']}}
                        kWh/mês
                    </td>
                    <td class="td @if($value['excedente'] > 0) is-green @else is-red @endif">{{$value['excedente']}}
                        kWh/mês
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>

    <div class="sumTable">
        <div id="tableTitle">Total</div>
        <table>
                <tr class="th"><td>CONSUMO</td></tr>
                <tr><td class="sumCol">{{$generationData['sum']['consumptionSum']}} kWh</td></tr>
                <tr class="th"><td>GERAÇÃO</td></tr>
                <tr><td class="sumCol">{{$generationData['sum']['generationSum']}} kWh</td></tr>
                <tr class="th"><td>SOBRA</td></tr>
                <tr><td class="sumCol">{{$generationData['sum']['generationSum'] - $generationData['sum']['consumptionSum']}} kWh</td></tr>
        </table>
    </div>



    <div id="incidence">{{ $incidence }} /m2</div>
    <div id="local">{{$proposal->client->addresses->first()->city->name_and_federal_unit}}</div>
{{--    <div id="estimatedMonth">{{$proposal->estimated_generation}} kWh/mês</div>--}}
{{--    <div id="estimatedAnual">{{$proposal->estimated_generation * 12}} kWh</div>--}}
    <div id="monthConsumption">{{$proposal->average_consumption}} kWh/mês</div>
    <div id="anualConsumption">{{$proposal->average_consumption * 12}} kWh</div>
    <div id="monthSurplus">
        {{$proposal->estimated_generation - $proposal->average_consumption}} kWh/mês
    </div>
    <div id="anualSurplus">
        {{ number_format(($proposal->estimated_generation * 12) - ($proposal->average_consumption * 12), 2) }}kWh
    </div>
</div>

<style>

    #systemPower, #estimatedGeneration {
        color: #F9880D;
        font-size: 28pt;
        font-weight: 600;
        position: absolute;
        top: 550px;
    }

    #systemPower {
        left: 250px;
    }

    #estimatedGeneration {
        left: 900px;
    }

    #incidence, #local, #estimatedMonth, #estimatedAnual, #monthConsumption, #anualConsumption, #monthSurplus, #anualSurplus {
        color: #F9880D;
        position: absolute;
        font-size: 14pt;
        font-weight: 900;
    }

    #incidence {
        top: 1640px;
        left: 1150px;
    }

    #local {
        top: 1640px;
        left: 300px;
    }

    #monthConsumption {
        top: 1900px;
        left: 300px;

    }

    #anualConsumption {
        top: 1900px;
        left: 1150px;

    }

    #monthSurplus {
        top: 2150px;
        left: 300px;

    }

    #anualSurplus {
        top: 2150px;
        left: 1150px;

    }

    table, th, td {
        border-collapse: collapse;
        border: 4px solid #F9880D;
        padding: 5px 22px;
        font-size: 10pt;
        color: #6c6c6c;
        margin: 0 !important;
    }

    #tableTitle {
        font-size: 18pt;
        font-weight: 800;
        color: #F9880D;
        margin: 14px 0;
        text-align: center;
    }

    .td {
        color: #F9880D;
        font-size: 10pt;
    }

    .table {
        position: absolute;
        top: 700px;
        left: 100px;
        margin: 0 !important;
        padding: 0;
    }

    th {
        font-size: 8pt !important;
    }

    .is-red {
        color: #F9880D !important;
    }

    .is-green {
        color: #008852 !important;
    }

    .sumTitle {
        font-weight: 900;
    }

    .sumCol {
        color: #008852;
    }

    .sumTable {
        position: absolute;
        top: 700px;
        left: 1100px;
    }

</style>
