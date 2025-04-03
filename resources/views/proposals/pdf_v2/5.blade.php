<div class="page page-break" style="background-image: url({{public_path('/img/proposal_v2/5.jpg')}})">
    @include('proposals.pdf_v2.number')

    <div id="systemPower">{{ $proposal->kwp }} <span class="minifiedTextForGeneration">kWp de potência total</span></div>
    <div id="estimatedGeneration">{{ ceil($proposal->estimated_generation) }} <span class="minifiedTextForGeneration">kWh/mês (média estimada ano)</span>
    </div>

    <div class="table">
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
        <table>
                <tr class="th"><td id="uth">Consumo</td></tr>
                <tr><td class="sumCol">{{$generationData['sum']['consumptionSum']}} kWh</td></tr>
                <tr class="th"><td id="uth">Geração</td></tr>
                <tr><td class="sumCol">{{$generationData['sum']['generationSum']}} kWh</td></tr>
                <tr class="th"><td id="uth">Excedente</td></tr>
                <tr><td class="sumCol">{{$generationData['sum']['generationSum'] - $generationData['sum']['consumptionSum']}} kWh</td></tr>
        </table>
    </div>



    <div id="incidence"><span class="minifiedTextForGeneration">Incidência solar</span> <br>{{ $incidence }} /m2</div>
    <div id="local"><span class="minifiedTextForGeneration">Resultado para </span><br>{{PHP_EOL . $proposal->client->addresses->first()->city->name_and_federal_unit}}</div>

</div>

<style>

    #systemPower, #estimatedGeneration {
        color: #ffb800;
        font-size: 20pt;
        font-weight: 600;
        position: absolute;
        top: 770px;
        background-color: #fff;
        border-radius: 10px;
        padding: 0 100px;
    }

    #systemPower {
        left: 130px;
    }

    #estimatedGeneration {
        left: 890px;
    }

    #incidence, #local  {
        color: #ffbc00;
        background-color: #fff;
        padding: 10px;
        border-radius: 50px;
        position: absolute;
        font-size: 14pt;
        font-weight: 900;
    }

    #local {
        top: 1485px;
        left: 1100px;
        padding: 5px 50px;
        width: 350px;
    }

    #incidence {
        top: 1350px;
        left: 1100px;
        padding: 5px 50px;
        width: 350px;
    }

    table, th, td {
        border-collapse: collapse;
        border: 2px solid #ffb600;
        padding: 8px 22px;
        font-size: 12pt;
        color: #ffffff;
        margin: 0 !important;
    }

    .td {
        color: #b37f00;
        font-size: 10pt;
    }

    .table {
        position: absolute;
        top: 870px;
        left: 100px;
        margin: 0 !important;
        padding: 0;
        width: 2000px;
    }

    th {
        font-size: 12pt !important;
        padding: 20px 0;
        background-color: #ffbc00;
    }

    .is-red {
        color: #ff4d00 !important;
    }

    .is-green {
        color: #00653e !important;
    }

    .sumCol {
        color: #00653e;
        text-align: center;
    }

    .sumTable {
        position: absolute;
        top: 870px;
        left: 1080px;
    }

    #uth {
        padding: 20px 140px !important;
        font-weight: bolder;
        text-align: center;
        background-color: #ffbc00;
        font-size: 12pt;
    }

    .minifiedTextForGeneration {
        font-size: 8pt !important;

    }

</style>
