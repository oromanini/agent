<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

@include('proposals.pdf_v2.1')
<div class="page page-break" style="background-image: url({{public_path('/img/proposal_v2/2.jpg')}})"></div>
@include('proposals.pdf_v2.3')
{{--@include('proposals.pdf_v2.4')--}}
@include('proposals.pdf_v2.5')
@include('proposals.pdf_v2.6')
@include('proposals.pdf_v2.7')
<div class="page page-break" style="background-image: url({{public_path('/img/proposal_v2/8.jpg')}})"></div>
<div class="page page-break" style="background-image: url({{public_path('/img/proposal_v2/9.jpg')}})"></div>
@include('proposals.pdf_v2.10')

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
