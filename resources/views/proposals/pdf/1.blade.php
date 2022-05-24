<div class="page page-break" style="background-image: url({{public_path('/img/proposal/1.jpg')}})">
    <div id="clientName" @if(count_chars($proposal->client->name) > 20) style="font-size: 22pt" @endif >
        {{$proposal->client->name}}
    </div>
    <div id="clientAddress">
        {{ $proposal->client->addresses->first()->city->name_and_federal_unit }}
    </div>
</div>

<style>

    #clientName {
        color: #fff;
        font-size: 34pt;
        position: absolute;
        top: 2000px;
        left: 50px;
        text-transform: uppercase;
        font-weight: 900;
    }

    #clientAddress {
        color: #fff;
        font-size: 18pt;
        position: absolute;
        top: 2220px;
        left: 50px;
        text-transform: uppercase;
        font-weight: 900;
    }

</style>
