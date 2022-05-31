<div class="columns is-flex is-flex-wrap-wrap">

    @foreach($kits as $kit)
        <div class="column is-12">
            <div class="accordion-tabs">
                <div class="tab">
                    <input type="checkbox" id="chck{{ $loop->iteration }}" class="checkbox-accordion">
                    <label class="tab-label" for="chck{{ $loop->iteration }}">Kit {{ $loop->iteration }}</label>
                    <div class="tab-content content">
                        <ul style="display: block !important;">
                            @foreach(kitByUuid($kit)['components'] as $component)
                            <li>{{ $component }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
