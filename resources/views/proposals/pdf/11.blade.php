<div class="page" style="background-image: url({{public_path('/img/proposal/11.jpg')}})">
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
        top: 1660px;
        left: 220px;
    }

    #agent_phone {
        top: 1780px;
        left: 220px;
    }
</style>
