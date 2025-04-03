<div class="page" style="background-image: url({{public_path('/img/proposal_v2/10.jpg')}})">
    @include('proposals.pdf_v2.number')

    <div id="agent_name">
        {{ $proposal->agent->name }}
    </div>
    <div id="agent_phone">
        {{ $proposal->agent->phone_number }}
    </div>
</div>
<style>
    #agent_name, #agent_phone {
        position: absolute;
        color: #fff;
        font-size: 18pt;
        font-weight: bolder;
    }

    #agent_name {
        top: 1760px;
        left: 240px;
    }

    #agent_phone {
        top: 1870px;
        left: 240px;
    }
</style>
