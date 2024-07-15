<div class="page page-break" style="background-image: url({{public_path('/img/leads/5.jpg')}})">
    <div id="panelImage">
        <img src="{{ public_path($panelBrandImage) }}" alt="" width="240">
    </div>
    <div id="panelQuantity">{{ $panelQuantity }} módulos</div>

    <div id="panelBrand">
            {{ $panelSpecs['brand'] }}
    </div>
    <div id="panelModel">
        {{ $panelSpecs['model'] }}
        <span class="minifiedText">Mono Half-Cell</span>
    </div>
    <div id="panelWarranty">
        {{ $panelSpecs['warranty'] }} anos (fábrica) / 25 anos (linear)
    </div>
    <div id="inverterImage">
        <img src="{{ public_path($inverterImage) }}" alt="" width="550">
    </div>
    <div id="inverterQuantity">
        1
    </div>
    <div id="inverterBrand">
        {{ $inverterSpecs['brand'] }}
    </div>
    <div id="inverterModel">
        {{ $inverterSpecs['model'] }}
    </div>
    <div id="inverterWarranty">
        {{ $inverterSpecs['warranty'] }} anos (de fábrica)
    </div>
    <div id="invertersOverload">
        {{ $overload }}
    </div>
</div>


<style>
    #panelImage {
        position: absolute;
        top: 600px;
        left: 65px;
    }

    #panelQuantity, #panelBrand, #panelModel, #panelWarranty, #invertersOverload {
        color: #fff;
        font-size: 16pt;
        position: absolute;
        left: 950px;
    }

    #panelQuantity {
        top: 670px;
    }

    #panelBrand {
        top: 750px;
    }

    #panelModel {
        top: 840px;
    }

    #panelWarranty {
        top: 940px;
    }

    #inverterImage {
        position: absolute;
        top: 1250px;
        left: 50px;
    }

    #inverterBrand, #inverterQuantity, #inverterModel, #inverterWarranty {
        color: #fff;
        font-size: 16pt;
        position: absolute;
        left: 950px;
    }

    #inverterQuantity {
        top: 1480px;
    }
    #inverterBrand {
        top: 1560px;
    }

    #inverterModel {
        top: 1655px;
    }

    #inverterWarranty {
        top: 1745px;
    }

    #invertersOverload {
        top: 1835px;
    }

</style>
