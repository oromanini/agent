<div class="columns is-flex is-flex-wrap-wrap">
    @if($proposal->is_manual)
        <div class="column is-12" style="padding: 0">
            <div class="accordion-tabs">
                <div class="tab">
                    <input type="checkbox" id="chck" class="checkbox-accordion">
                    <label class="tab-label" for="chck">Componentes do Kit Gerador</label>
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
        @foreach($kits as $kit)
            <div class="column is-12" style="padding: 0">
                <div class="accordion-tabs">
                    <div class="tab">
                        <input type="checkbox" id="chck{{ $loop->iteration }}" class="checkbox-accordion">
                        <label class="tab-label" for="chck{{ $loop->iteration }}">Kit {{ $loop->iteration }}</label>
                        <div class="tab-content content">
                            <ul style="display: block !important;">
{{--                                <li>{{ $kit }}</li>--}}
                                @foreach(kitByUuid($kit)['components'] as $component)
                                    <li>{{ $component }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
