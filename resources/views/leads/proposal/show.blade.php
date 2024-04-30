@extends('base')

@php
    function cleanPhone(\App\Models\Lead $lead): string
    {
        return preg_replace("/[^0-9]/", "", $lead->phone_number);
    }

    function deadline(\App\Models\Lead $lead): array
    {
        $now = new DateTime();
        $diff = $now->diff($lead->updated_at);
        $stringDiff = '';

        $diff->d > 0 && $stringDiff .= $diff->d . ' dias, ';
        $diff->h > 0 && $stringDiff .= $diff->h . ' horas e ';
        $stringDiff .= $diff->i . ' minutos';

        $color = $diff->d >= 2 ? '#EF2A2A' : '#B3EF2A';

        return ['color' => $color, 'text' => "A {$stringDiff}"];
    }

@endphp

@section('content')
    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            @include('leads.proposal.show_components.header')
            <hr>
            <div class="columns">
                @include('leads.proposal.show_components.status_dropdown')
                @include('leads.proposal.show_components.cards')
            </div>

            <hr>

            <div class="columns">
                <div class="column is-3">
                    <label for="created_at" class="label">
                        <ion-icon name="calendar-outline"></ion-icon>&nbsp;
                        Data de criação
                    </label>
                    <span>{{ date_format($lead->created_at, 'd/m/Y') }} às {{ date_format($lead->created_at, 'H:i') }}h</span>
                </div>
                <div class="column is-3">
                    <label for="created_at" class="label">
                        <ion-icon name="logo-whatsapp"></ion-icon>&nbsp;
                        Whatsapp
                    </label>
                    <a href="https://api.whatsapp.com/send?phone={{ cleanPhone($lead) }}">{{ $lead->phone_number }}</a>
                </div>
                <div class="column is-3">
                    <label for="created_at" class="label">
                        <ion-icon name="home-outline"></ion-icon>&nbsp;
                        Tipo de telhado
                    </label>
                    <span>{{ \App\Enums\RoofStructure::tryFrom($lead->roof_structure)->name }}</span>
                </div>
                <div class="column is-3">
                    <label for="created_at" class="label">
                        <ion-icon name="cash-outline"></ion-icon>&nbsp;
                        Preço do KwH
                    </label>
                    <span>R$ {{ $lead->kwh_price }}</span>
                </div>
            </div>
            <div class="columns">
                <div class="column is-3">
                    <label for="created_at" class="label">
                        <ion-icon name="accessibility-outline"></ion-icon>&nbsp;
                        Responsável
                    </label>
                    <span>{{ \App\Models\User::find($lead->user_id)->name }}</span>
                </div>
                <div class="column is-3">
                    <label for="created_at" class="label">
                        <ion-icon name="business-outline"></ion-icon>&nbsp;
                        Localidade
                    </label>
                    <span>{{ (\App\Models\City::find($lead->city_id))->name_and_federal_unit }}</span>
                </div>
                <div class="column is-6">
                    <label for="created_at" class="label">
                        <ion-icon name="alarm-outline"></ion-icon>&nbsp;
                        Última atualização
                    </label>
                    <div class="is-flex is-flex-direction-row">
                        <span id="deadline-circle" style="background-color: {{ deadline($lead)['color'] }};" class="circle"></span>
                        {{ deadline($lead)['text'] }}
                    </div>
                </div>
            </div>
            <hr>

            <div class="columns">
                <div class="column is-12">
                    @include('leads.proposal.show_components.components')
                </div>
            </div>
        </div>
    </div>
    <style>
        #deadline-circle {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 10px;
        }
    </style>
@endsection

