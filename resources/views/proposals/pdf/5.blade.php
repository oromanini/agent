<div class="page page-break" style="background-image: url({{public_path('/img/proposal/5.jpg')}})">
    <div id="panelImage">
        <img src="{{ public_path($panelBrandImage) }}" alt="" width="450">
    </div>
    <div id="panelQuantity">{{ $proposal->number_of_panels }} módulos</div>
    <div id="panelBrand">
        @if($proposal->is_manual)
        {{ \App\Enums\PanelBrands::fromValue((int)$manualData['panel_brand'])->description }}
        @else
        @endif
        {{--        <span class="minifiedText">New Energy Tec. Co.</span>--}}
    </div>
    <div id="panelModel">
        @if($proposal->is_manual)
            {{ $manualData['panel_power'] }}W
        @else
        @endif
        <span class="minifiedText">Mono Half-Cell</span>
    </div>
    <div id="panelWarranty">
        @if($proposal->is_manual)
            {{ $manualData['panel_warranty'] }} anos
        @else
        @endif
    </div>
    <div id="inverterImage">
        <img src="{{ public_path($inverterImage) }}" alt="" width="450">
    </div>
    <div id="inverterBrand">
        @if($proposal->is_manual)
            {{ \App\Enums\InverterBrands::fromValue((int)$manualData['inverter_brand'])->description }}
        @else
        @endif
    </div>
    <div id="inverterModel">
        @if($proposal->is_manual)
            {{ $manualData['inverter_power'] }}KW <span class="minified-text">{{ $manualData['inverter_model'] }} </span>
        @else
        @endif
    </div>
    <div id="inverterWarranty">
        @if($proposal->is_manual)
           {{ $manualData['inverter_warranty'] }} anos
        @else
        @endif
    </div>
</div>

<style>
    #panelImage {
        position: absolute;
        top: 700px;
        left: 40px;
    }

    #panelQuantity, #panelBrand, #panelModel, #panelWarranty {
        color: #6b7280;
        font-size: 16pt;
        position: absolute;
        left: 1220px;
    }

    #panelQuantity {
        top: 660px;
    }

    #panelBrand {
        top: 770px;
    }

    #panelModel {
        top: 880px;
    }

    #panelWarranty {
        top: 980px;
    }

    #inverterImage {
        position: absolute;
        top: 1460px;
        left: 1150px;
    }

    #inverterBrand, #inverterModel, #inverterWarranty {
        color: #6b7280;
        font-size: 16pt;
        position: absolute;
        left: 520px;
    }

    #inverterBrand {
        top: 1530px;
    }

    #inverterModel {
        top: 1640px;
    }

    #inverterWarranty {
        top: 1760px;
    }

</style>
