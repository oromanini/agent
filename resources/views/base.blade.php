<!DOCTYPE html>
<html lang="br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agente Alluz</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script src="{{ mix('js/app.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
<section class="section">
    @include('loader')
    <div class="columns">
        <div class="sidebar-column column is-1">
            @include('sidebar')
        </div>
        <div class="content-column column is-11">
            @yield('content')
            @include('message')
        </div>
    </div>
</section>
@include('footer')
@include('whatsapp')

<script>
    $(window).on('load', function () {
        setTimeout(function () {
            $('#loader').hide();
        }, 500 )

    });
</script>

</body>
</html>
