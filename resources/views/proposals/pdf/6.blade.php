<div class="page page-break" style="background-image: url({{public_path('/img/proposal/6.jpg')}})">
    <div id="components">
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
        top: 700px;
        left: 330px;
        max-width: 1000px;
    }

    #components * {
        font-size: 8pt;
        color: #1a202c;
        line-height: 60px;
        font-weight: 900;
    }
</style>
