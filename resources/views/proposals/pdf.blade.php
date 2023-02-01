<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

@include('proposals.pdf.1')
<div class="page page-break" style="background-image: url({{public_path('/img/proposal/2.jpg')}})"></div>
<div class="page page-break" style="background-image: url({{public_path('/img/proposal/3.jpg')}})"></div>
@include('proposals.pdf.5')
@include('proposals.pdf.7')
@include('proposals.pdf.6')
@include('proposals.pdf.8')
@include('proposals.pdf.9')
<div class="page page-break" style="background-image: url({{public_path('/img/proposal/7.jpg')}})"></div>
@include('proposals.pdf.11')
<style>
    html {
        margin: 0;
    }

    .page-break {
        page-break-after: always;
    }

    .page {
        background-size: cover;
        background-repeat: no-repeat;
        min-height: 100vh;
    }

    .minifiedText {
        font-size: 8pt !important;
    }
</style>
