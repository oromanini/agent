<div class="columns is-flex is-flex-wrap-wrap">
    @if($proposal->is_manual)
        <div class="column is-12" style="padding: 0">
            <div class="accordion-tabs">
                <div class="tab">
                    <input type="checkbox" id="chck2" class="checkbox-accordion">
                    <label class="tab-label" for="chck2">Componentes do Kit Gerador</label>
                    <div class="tab-content content">
                        <ul style="display: block !important;">
                            @foreach($kits as $component)
                                <li>{{ $component }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="column is-12" style="padding: 0">
            <div class="accordion-tabs">
                <div class="tab">
                    <input type="checkbox" id="chck" class="checkbox-accordion">
                    <label class="tab-label" for="chck">Kit</label>
                    <div class="tab-content content">
                        <ul style="display: block !important;">
                            @foreach($kits as $component)
                                <li>{{ $component }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
