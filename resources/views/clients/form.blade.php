@extends('base')

@section('content')

    <div class="container is-fluid">
        <div class="box">

            <form action="{{ isset($client) ? route('client.update', [$client->id]) : route('client.store') }}"
                  enctype="multipart/form-data" method="POST">
                @if(isset($client))
                    @method('PUT')
                @else
                    @method('POST')
                @endif
                @csrf

                <div class="columns mt-2 ml-1">
                    <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30"
                                           alt=".."> {{ isset($client) ? 'Editar cliente' : 'Novo cliente' }}</h3>
                </div>
                <div class="columns">
                    <div class="title-bottom-line" style="margin-left: 50px"></div>
                </div>

                @include('clients.form_inputs')

                <div class="is-flex is-justify-content-center">
                    <button type="submit" class="button is-primary is-large">
                        <ion-icon name="save-outline"></ion-icon> &nbsp;{{ isset($client) ? 'Atualizar' : 'Salvar' }}
                    </button>
                    <a href="{{ route('client.index') }}" class="button is-info is-large ml-2">Voltar</a>
                </div>

            </form>
        </div>

        @if(isset($client))
            @include('clients.address_table')
            @include('clients.address_modal')
        @endif

    </div>

    <script>

        $(function () {
            const owner_document = document.querySelector('#owner_document input[type=file]');
            const electricity_bill = document.querySelector('#electricity_bill input[type=file]');

            owner_document.onchange = () => {
                if (owner_document.files.length > 0) {
                    const fileName = document.querySelector('#owner_document .file-name');
                    fileName.textContent = owner_document.files[0].name;
                }
            }

            electricity_bill.onchange = () => {
                if (electricity_bill.files.length > 0) {
                    const fileName = document.querySelector('#electricity_bill .file-name');
                    fileName.textContent = electricity_bill.files[0].name;
                }
            }
        })

    </script>

@endsection

