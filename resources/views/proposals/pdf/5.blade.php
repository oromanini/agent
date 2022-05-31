<div class="page page-break" style="background-image: url({{public_path('/img/proposal/5.jpg')}})">
    <div id="panelImage">
        <img src="{{ public_path($panelBrandImage) }}" alt="" width="240">
    </div>
    <div id="panelQuantity">{{ $proposal->number_of_panels }} módulos</div>
    <div id="panelBrand">
        @if($proposal->is_manual)
        {{ \App\Enums\PanelBrands::fromValue((int)$manualData['panel_brand'])->description }}
        @else
            {{ $firstKit['technical_description']['panel_specs']['panel_brand'] }}
        @endif
        {{--        <span class="minifiedText">New Energy Tec. Co.</span>--}}
    </div>
    <div id="panelModel">
        @if($proposal->is_manual)
            {{ $manualData['panel_power'] }}W
        @else
            {{ $firstKit['technical_description']['panel_specs']['panel_power'] }} W
        @endif
        <span class="minifiedText">Mono Half-Cell</span>
    </div>
    <div id="panelWarranty">
        @if($proposal->is_manual)
            {{ $manualData['panel_warranty'] }} anos
        @else
            12 anos
        @endif
    </div>
    <div id="inverterImage">
        <img src="{{ public_path($inverterImage) }}" alt="" width="550">
    </div>
    <div id="inverterQuantity">
        @if($proposal->is_manual)
            1
        @else
            1
{{--            {{ $invertersCount }}--}}
        @endif
    </div>
    <div id="inverterBrand">
        @if($proposal->is_manual)
            {{ \App\Enums\InverterBrands::fromValue((int)$manualData['inverter_brand'])->description }}
        @else
            {{ $firstKit['technical_description']['inverter_brand'] }}
        @endif
    </div>
    <div id="inverterModel">
        @if($proposal->is_manual)
            {{ $manualData['inverter_power'] }}KW <span class="minified-text">{{ $manualData['inverter_model'] }} </span>
        @else
            {{ $firstKit['technical_description']['inverter_model'] }}
{{--            {{ $inverterModels }}--}}
        @endif
    </div>
    <div id="inverterWarranty">
        @if($proposal->is_manual)
           {{ $manualData['inverter_warranty'] }} anos
        @else
            @if($firstKit['technical_description']['inverter_brand'] == 'Growatt')
                10 anos
            @else
                7 anos
            @endif
        @endif
    </div>
</div>

<style>
    #panelImage {
        position: absolute;
        top: 600px;
        left: 65px;
    }

    #panelQuantity, #panelBrand, #panelModel, #panelWarranty {
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
        top: 930px;
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
        top: 1400px;
    }
    #inverterBrand {
        top: 1490px;
    }

    #inverterModel {
        top: 1580px;
    }

    #inverterWarranty {
        top: 1670px;
    }

</style>
