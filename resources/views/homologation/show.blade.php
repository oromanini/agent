@extends('base')

@section('content')
    <div class="container is-fluid overflow-auto">
        <div class="box overflow-auto">
            <div class="is-flex is-flex-direction-row is-justify-content-space-between mt-2 ml-1">
                <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt=".."> Homologação</h3>
                <a class="button is-primary" style="font-size: 16pt"
                   href="{{ route('approval.show', [$homologation->proposal->id]) . '#technical' }}">
                    <ion-icon name="eye-outline"></ion-icon>
                    Ver vistoria
                </a>
            </div>
            <br>
            <div class="columns">
                <div class="column is-4">
                    <span class="tag is-info is-light" style="font-size: 16pt">
                        {{ 'Proposta #' . $homologation->proposal->id . ' - ' .$homologation->proposal->client->name }}
                    </span>
                </div>
            </div>
            <div class="columns ml-1">
                <span class="tag is-info" style="font-size: 16pt">
                    <strong style="color: #fff">Status: </strong> &nbsp; {{ $homologation->status->name }}
                </span>
                @if($homologation->status->is_final)
                    <span class="tag {{ getSubstatusColor($homologation->is_approved_on_dealership) }} ml-1"
                          style="font-size: 16pt">
                        <strong style="color: #fff">{{ 'Concessionária: '}}</strong> &nbsp;{{ $homologation->is_approved_on_dealership }}
                    </span>
                @endif
            </div>
            <br>
            <form action="{{ route('homologation.update', [$homologation->id]) }}" method="post"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row status-box">
                    @foreach(json_decode($homologation->checklist, true) as $key => $value)
                        <span class="span-status">{{ $key }} &nbsp;
                            <ion-icon class="{{ $value ? 'elipse-green' : 'elipse-red' }}"
                                      name="{{ $value ? 'checkmark-circle' : 'close-circle' }}"></ion-icon>
                        </span>
                    @endforeach

                </div>
                <br>
                @include('proposals.show.cards')

                @include('homologation.client_data')
                <hr>
                @include('proposals.show.kit_data')

                <hr>
                <hr>
                <div class="columns">
                    <div class="column is-3">
                        <div class="field">
                            <label id="protocol_approval_date" for="protocol_approval_date" class="label">
                                <ion-icon class="elipse-blue" name="ellipse"></ion-icon>
                                Data de protocolação</label>
                            <div class="control">
                                <input name="protocol_approval_date" id="protocol_approval_date"
                                       class="input @error('protocol_approval_date') is-danger @enderror" type="date"
                                       value="{{ isset($homologation->protocol_approval_date) ? $homologation->protocol_approval_date->toDateString() : '' }}">
                                @error('protocol_approval_date')<span
                                    class="error-message">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="column is-3">
                        <label for="trt_pay_order" class="label">
                            <ion-icon class="elipse-blue" name="ellipse"></ion-icon>
                            Boleto para pagamento TRT</label>
                        @if(isset($homologation->trt_pay_order))
                            <a href="/storage/{{ str_replace('public/', '', $homologation->trt_pay_order) }}"
                               class="button is-danger" target="_blank">
                                <ion-icon name="eye-outline"></ion-icon>
                                Visualizar Boleto</a>
                        @else
                            <div class="file has-name">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="trt_pay_order" id="trt_pay_order">
                                    <span class="file-cta">
                                  <span class="file-icon">
                                    <ion-icon name="folder-outline"></ion-icon>
                                  </span>
                                  <span class="file-label">
                                    Escolher arquivo…
                                  </span>
                                </span>
                                    <span class="file-name">
                                    Nenhum arquivo selecionado
                                </span>
                                </label>
                            </div>
                        @endif
                    </div>
                    <div class="column is-3 {{ !isset($homologation->trt_pay_order) ? 'is-hidden' : '' }}">
                        <label for="proof_of_bill_payment" class="label">
                            <ion-icon class="elipse-yellow" name="ellipse"></ion-icon>
                            Comprovante de pagamento TRT</label>
                        @if(isset($homologation->proof_of_bill_payment))
                            <a href="/storage/{{ str_replace('public/', '', $homologation->proof_of_bill_payment) }}"
                               class="button is-danger" target="_blank">
                                <ion-icon name="eye-outline"></ion-icon>
                                Visualizar Comprovante</a>
                        @else
                            <div class="file has-name">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="proof_of_bill_payment"
                                           id="proof_of_bill_payment">
                                    <span class="file-cta">
                                  <span class="file-icon">
                                    <ion-icon name="folder-outline"></ion-icon>
                                  </span>
                                  <span class="file-label">
                                    Escolher arquivo…
                                  </span>
                                </span>
                                    <span class="file-name">
                                    Nenhum arquivo selecionado
                                </span>
                                </label>
                            </div>
                        @endif
                    </div>
                    <div class="column is-3 {{ !isset($homologation->proof_of_bill_payment) ? 'is-hidden' : '' }}">
                        <label for="access_opinion_form" class="label">
                            <ion-icon class="elipse-blue" name="ellipse"></ion-icon>
                            Parecer de acesso</label>
                        @if(isset($homologation->access_opinion_form))
                            <a href="/storage/{{ str_replace('public/', '', $homologation->access_opinion_form) }}"
                               class="button is-danger" target="_blank">
                                <ion-icon name="eye-outline"></ion-icon>
                                Visualizar Formulário</a>
                        @else
                            <div class="file has-name">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="access_opinion_form"
                                           id="access_opinion_form">
                                    <span class="file-cta">
                                  <span class="file-icon">
                                    <ion-icon name="folder-outline"></ion-icon>
                                  </span>
                                  <span class="file-label">
                                    Escolher arquivo…
                                  </span>
                                </span>
                                    <span class="file-name">
                                    Nenhum arquivo selecionado
                                </span>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-3 {{ !isset($homologation->access_opinion_form) ? 'is-hidden' : '' }}">
                        <label for="signed_access_opinion_form" class="label">
                            <ion-icon class="elipse-yellow" name="ellipse"></ion-icon>
                            Formulário de Parecer de acesso Assinado</label>
                        @if(isset($homologation->signed_access_opinion_form))
                            <a href="/storage/{{ str_replace('public/', '', $homologation->signed_access_opinion_form) }}"
                               class="button is-danger" target="_blank">
                                <ion-icon name="eye-outline"></ion-icon>
                                Formulário assinado</a>
                        @else
                            <div class="file has-name">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="signed_access_opinion_form"
                                           id="signed_access_opinion_form">
                                    <span class="file-cta">
                                  <span class="file-icon">
                                    <ion-icon name="folder-outline"></ion-icon>
                                  </span>
                                  <span class="file-label">
                                    Escolher arquivo…
                                  </span>
                                </span>
                                    <span class="file-name">
                                    Nenhum arquivo selecionado
                                </span>
                                </label>
                            </div>
                        @endif
                    </div>
                    <div class="column is-3">
                        <label for="single_line_project" class="label">
                            <ion-icon class="elipse-blue" name="ellipse"></ion-icon>
                            Diagrama Unifilar</label>
                        @if(isset($homologation->single_line_project))
                            <a href="/storage/{{ str_replace('public/', '', $homologation->single_line_project) }}"
                               class="button is-danger" target="_blank">
                                <ion-icon name="eye-outline"></ion-icon>
                                Ver Unifilar</a>
                        @else
                            <div class="file has-name">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="single_line_project"
                                           id="single_line_project">
                                    <span class="file-cta">
                                  <span class="file-icon">
                                    <ion-icon name="folder-outline"></ion-icon>
                                  </span>
                                  <span class="file-label">
                                    Escolher arquivo…
                                  </span>
                                </span>
                                    <span class="file-name">
                                    Nenhum arquivo selecionado
                                </span>
                                </label>
                            </div>
                        @endif
                    </div>
                    <div class="column is-3">
                        <label for="payment_voucher" class="label">
                            <ion-icon class="elipse-yellow" name="ellipse"></ion-icon>
                            Pagamento ao homologador</label>
                        @if(isset($homologation->payment_voucher))
                            <a href="/storage/{{ str_replace('public/', '', $homologation->payment_voucher) }}"
                               class="button is-danger" target="_blank">
                                <ion-icon name="eye-outline"></ion-icon>
                                Ver Comprovante</a>
                        @else
                            <div class="file has-name">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="payment_voucher"
                                           id="single_line_project">
                                    <span class="file-cta">
                                  <span class="file-icon">
                                    <ion-icon name="folder-outline"></ion-icon>
                                  </span>
                                  <span class="file-label">
                                    Escolher arquivo…
                                  </span>
                                </span>
                                    <span class="file-name">
                                    Nenhum arquivo selecionado
                                </span>
                                </label>
                            </div>
                        @endif
                    </div>
                    <div class="column is-3 {{ !isset($homologation->signed_access_opinion_form) ? 'is-hidden' : '' }}">
                        <div class="field">
                            <label for="type" class="label">
                                <ion-icon class="elipse-blue" name="ellipse"></ion-icon>
                                Status da Concessionária</label>
                            <div
                                class="select is-multiline is-fullwidth">
                                <select id="is_approved_on_dealership" name="is_approved_on_dealership">
                                    <option
                                        value="Em Análise" {{ $homologation->is_approved_on_dealership == 'Em Análise' ? 'selected' : '' }}>
                                        <ion-icon class="elipse-yellow" name="ellipse"></ion-icon>
                                        Em Análise
                                    </option>
                                    <option
                                        value="Aprovado" {{ $homologation->is_approved_on_dealership == 'Aprovado' ? 'selected' : '' }}>
                                        <ion-icon class="elipse-green" name="ellipse"></ion-icon>
                                        Aprovado
                                    </option>
                                    <option
                                        value="Reprovado" {{ $homologation->is_approved_on_dealership == 'Reprovado' ? 'selected' : '' }}>
                                        <ion-icon class="elipse-red" name="ellipse"></ion-icon>
                                        Reprovado
                                    </option>
                                </select>
                            </div>
                            @error('type')<span class="error-message">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <hr>
                <div class="columns">
                    <div class="column is-12">
                        <label id="notes" for="notes" class="label">Observações</label>
                        <textarea name="notes" id="notes"
                                  class="textarea" type="text"
                                  placeholder="Digite o documento">{{ $homologation->notes }}</textarea>
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-flex is-justify-content-center">
                        <button type="submit" class="button is-primary is-large">
                            <ion-icon name="save-outline"></ion-icon> &nbsp;Salvar
                        </button>&nbsp;
                        <a class="button is-warning is-large" href="{{ route('homologation.index') }}">Voltar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
