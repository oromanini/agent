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
        top: 600px;
        left: 300px;
        max-width: 1000px;
    }

    #components * {
        font-size: 8pt;
        color: #6b7280;
        line-height: 60px;
    }
</style>
