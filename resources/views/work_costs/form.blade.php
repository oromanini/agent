@extends('base')

@section('content')

    <section class="section">
        <div class="container">
            <div class="columns">
                <div class="column">
                    <div class="box">
                        <h3 class="title is-4 mb-5" style="display: flex; align-items: center;">
                            <img src="/img/logo/alluz-icon.png" width="30" alt=".." style="margin-right: 10px;">
                            Editando Custo: {{ $workCost->classification_name }}
                        </h3>

                        @if ($errors->any())
                            <div class="notification is-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('work_costs.update', $workCost->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="field">
                                <label class="label">Classificação</label>
                                <div class="control">
                                    <input class="input" type="text" value="{{ $workCost->classification_name }}" disabled>
                                    <input type="hidden" name="classification" value="{{ $workCost->classification }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Custos (Formato JSON)</label>
                                <div>
                                    <textarea name="costs" class="textarea is-family-monospace" rows="4">{{ old('costs', $workCost->costs) }}</textarea>
                                </div>
                                <p class="help">Edite os valores. Para percentuais, use o formato "1.7%" ou "17%".</p>
                            </div>

                            <div class="field is-grouped mt-5">
                                <div class="control">
                                    <button type="submit" class="button is-link">Salvar Alterações</button>
                                </div>
                                <div class="control">
                                    <a href="{{ route('work_costs.index') }}" class="button is-light">Cancelar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
