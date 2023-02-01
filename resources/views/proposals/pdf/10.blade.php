<div class="page page-break" style="background-image: url({{public_path('/img/proposal/10.jpg')}})">
    <div id="alluzSignature" style="color: #765500">
        Alluz Energia e Sustentabilidade
        <br>CNPJ: 34.782.317/0001-49
    </div>
    <div id="clientSignature" style="color: #765500">
        {{ $proposal->client->name }}
        <br>CPF/CNPJ: {{ $proposal->client->document }}
    </div>
</div>

<style>
    #alluzSignature {
        position: absolute;
        top: 2090px;
        left: 130px;
        font-size: 13pt;
    }

    #clientSignature {
        position: absolute;
        top: 2090px;
        left: 930px;
        font-size: 13pt;
    }
</style>
