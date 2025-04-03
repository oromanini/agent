<div class="page page-break" style="background-image: url({{public_path('/img/proposal_v2/7.jpg')}})"><div id="components">
        @include('proposals.pdf_v2.number')

        <ul>
            @foreach($components as $component)
                <li>{{ $component }}</li>
            @endforeach
        </ul>
    </div>
</div>

<style>
    #components {
        position: absolute;
        top: 800px;
        left: 150px;
        max-width: 1300px;
    }

    #components * {
        font-size: 10pt;
        color: #e4a200;
        line-height: 60px;
        font-weight: 900;
    }
</style>
