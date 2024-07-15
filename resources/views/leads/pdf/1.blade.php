<div class="page page-break" style="background-image: url({{public_path('/img/leads/1.jpg')}})">
    <div @if(count_chars($lead->name) > 20) id="clientName" @else id="clientName2" @endif >
        {{ $lead->name }}
    </div>
    <div id="clientAddress">
        {{ $city->name_and_federal_unit }}
    </div>
</div>


<style>

    #clientName {
        font-size: 26pt !important;
        position: absolute;
        top: 2000px;
        left: 50px;
        text-transform: uppercase;
        font-weight: 900;
        margin-right: 40px;
        color: #fff;
    }

    #clientName2 {
        color: #fff;
        font-size: 40pt !important;
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
