<div class="page page-break" style="background-image: url({{public_path('/img/proposal/8.jpg')}})">
    <div id="systemPower">{{ $proposal->kwp }} kWp</div>
    <div id="estimatedGeneration">{{ ceil($proposal->estimated_generation) }} kWh/mês <span class="minifiedText">(estimado)</span></div>
    <div id="incidence">{{ $incidence }} /m2</div>
    <div id="local">{{$proposal->client->addresses->first()->city->name_and_federal_unit}}</div>
    <div id="estimatedMonth">{{$proposal->estimated_generation}} kWh/mês</div>
    <div id="estimatedAnual">{{$proposal->estimated_generation * 12}} kWh</div>
    <div id="monthConsumption">{{$proposal->average_consumption}} kWh/mês</div>
    <div id="anualConsumption">{{$proposal->average_consumption * 12}} kWh</div>
    <div id="monthSurplus">
        {{$proposal->estimated_generation - $proposal->average_consumption}} kWh/mês
    </div>
    <div id="anualSurplus">
        {{($proposal->estimated_generation * 12) - ($proposal->average_consumption * 12)}}kWh</div>
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
        left: 250px ;
    }

    #estimatedGeneration {
        left: 900px ;
    }

    #incidence, #local, #estimatedMonth, #estimatedAnual, #monthConsumption, #anualConsumption, #monthSurplus, #anualSurplus {
        color: #6b7280;
        position: absolute;
        font-size: 14pt;
        left: 850px;
    }

    #incidence {
        top: 1797px;
    }

    #local {
        top: 1860px;
    }

    #estimatedMonth {
        top: 1920px;
    }

    #estimatedAnual {
        top: 1980px;
    }

    #monthConsumption {
        top: 2040px;
    }

    #anualConsumption {
        top: 2105px;
    }

    #monthSurplus {
        top: 2165px;
    }
    #anualSurplus {
        top: 2225px;
    }

</style>
