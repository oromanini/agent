<div class="page page-break" style="background-image: url({{public_path('/img/proposal_v2/1.jpg')}})">
    <div @if(strlen($proposal->client->name) > 25) id="clientName" @else id="clientName2" @endif >
        {{$proposal->client->name}}
    </div>
    <div id="clientAddress">
        {{ $proposal->client->addresses->first()->city->name_and_federal_unit }}
    </div>
</div>

<style>

    #clientName {
        color: #fff;
        font-size: 24pt !important;
        position: absolute;
        top: 1985px;
        left: 110px;
        text-transform: uppercase;
        font-weight: bolder;
        margin-right: 200px;
        font-family: Helvetica sans-serif;
    }

    #clientName2 {
        color: #fff;
        font-size: 36pt !important;
        position: absolute;
        top: 1990px;
        left: 110px;
        text-transform: uppercase;
        font-weight: 100;
    }

    #clientAddress {
        color: #ffffff;
        font-size: 18pt;
        position: absolute;
        top: 2200px;
        left: 110px;
        font-weight: lighter;
    }

</style>

