<div class="page page-break" style="background-image: url({{public_path('/img/proposal_v2/6.jpg')}})">
    <div id="panelImage">
        <img src="{{ $panelImage }}" alt="" width="400">
    </div>
    <div id="panelQuantity">{{ $proposal->number_of_panels }} módulos</div>

    <div id="panelBrand">
        @if($proposal->is_manual)
        {{ \App\Enums\PanelBrands::from((int)$manualData['panel_brand'])->name }}
        @else
            {{ jsonToArray($firstKit['panel_specs'])['brand'] }}
        @endif
    </div>
    <div id="panelModel">
        @if($proposal->is_manual)
            {{ $manualData['panel_power'] }}W
        @else
            {{ jsonToArray($firstKit['panel_specs'])['power'] }}W
        @endif
    </div>
    <div id="panelWarranty">
        @if($proposal->is_manual)
            {{ $manualData['panel_warranty'] }} anos
        @else
        {{jsonToArray($firstKit['panel_specs'])['warranty']}} anos
        @endif
    </div>
    <div id="inverterImage">
        <img src="{{ $inverterImage }}" alt="" width="540">
    </div>
    <div id="inverterQuantity">
        @if($proposal->is_manual)
            {{ $manualData['inverter_quantity'] ?? 1 }}
        @else
            {{ $invertersCount }}
        @endif
    </div>
    <div id="inverterBrand">
        @if($proposal->is_manual)
            {{ \App\Enums\InverterBrands::from((int)$manualData['inverter_brand'])->name }}
        @else
            {{ jsonToArray($firstKit['inverter_specs'])['brand'] }}
        @endif
    </div>
    <div id="inverterModel">
        @if($proposal->is_manual)
            {{ $manualData['inverter_power'] }}KW <span class="minified-text">{{ $manualData['inverter_model'] }} </span>
        @else
            {{
                jsonToArray($firstKit['inverter_specs'])['model'] !== 'Dados Técnicos do Inve'
                ? jsonToArray($firstKit['inverter_specs'])['model']
                : "On-Grid"
            }}
        @endif
    </div>
    <div id="inverterWarranty">
        @if($proposal->is_manual)
           {{ $manualData['inverter_warranty'] }} anos
        @else
            {{ jsonToArray($firstKit['inverter_specs'])['warranty'] }} anos (fabricação)
        @endif
    </div>
    <div id="invertersOverload">
        {{ $overload }}
    </div>
</div>

<style>
    #panelImage {
        position: absolute;
        top: 600px;
        left: 225px;
    }

    #panelQuantity, #panelBrand, #panelModel, #panelWarranty, #invertersOverload {
        color: #e4a200;
        font-size: 16pt;
        position: absolute;
    }

    #panelQuantity {
        top: 770px;
        left: 1000px;

    }

    #panelBrand {
        top: 885px;
        left: 1060px;

    }

    #panelModel {
        top: 1000px;
        left: 1000px;
    }

    #panelWarranty {
        top: 1110px;
        left: 1020px;
    }

    #inverterImage {
        position: absolute;
        top: 1370px;
        left: 150px;
        background-size: contain;
    }

    #inverterBrand, #inverterQuantity, #inverterModel, #inverterWarranty {
        color: #e4a200;
        font-size: 16pt;
        position: absolute;
    }

    #inverterQuantity {
        top: 1355px;
        left: 1015px;
    }
    #inverterBrand {
        top: 1468px;
        left: 1075px;
    }

    #inverterModel {
        top: 1580px;
        left: 1015px;
    }

    #inverterWarranty {
        top: 1695px;
        left: 1025px;
    }

    #invertersOverload {
        top: 1805px;
        left: 995px;
    }

</style>
