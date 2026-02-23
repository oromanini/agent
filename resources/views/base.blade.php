<!DOCTYPE html>
<html lang="br">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agente Alluz</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script src="{{ mix('js/app.js') }}"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
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
{{--@include('whatsapp')--}}

<script>
    $(window).on('load', function () {
        setTimeout(function () {
            $('#loader').hide();
        }, 500 )

    });
</script>

@stack('scripts')
</body>
</html>
