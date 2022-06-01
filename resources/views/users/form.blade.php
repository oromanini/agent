@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <div class="columns mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Novo usuário</h3>
            </div>
            <form
                action="@if(isset($agent)){{ route('user.update', [$agent->id]) }}@else {{ route('user.store') }} @endif"
                method="post">
                @if(isset($agent))
                    @method('PUT')
                @else
                    @method('POST')
                @endif
                @csrf
                <div class="columns" style="margin-top: 50px">
                    @include('users.fields.name')
                    @include('users.fields.cpf')
                    @include('users.fields.cnpj')
                    @include('users.fields.email')
                    @include('users.fields.password')
                </div>
                <div class="columns">
                    @include('users.fields.phone_number')
                    @include('users.fields.city_state')
                    @include('users.fields.ascendant')
                    @include('users.fields.contract')
                </div>
                <hr>
                <div class="columns is-flex is-justify-content-center" style="margin: 20px 0">
                    @include('users.fields.submit_button')
                </div>
            </form>
        </div>
    </div>
@endsection
