@if(!isset($agent) || is_null($agent->contract))
    <div class="column is-3 is-fullwidth">
        <label class="label" for="">Contrato</label>
        <div class="file is-danger has-name">
            <label class="file-label">
                <input class="file-input" type="file" name="resume">
                <span class="file-cta">
                                  <span class="file-icon">
                                      <ion-icon name="cloud-upload-outline"></ion-icon>
                                  </span>
                                  <span class="file-label">
                                      Selecionar pdf…
                                  </span>
                                </span>
                <span class="file-name">
                                  nenhum arquivo selecionado
                                </span>
            </label>
        </div>
    </div>
@else
    <div class="column is-2">
        <label class="label" for="">Contrato (PDF)</label>
        <a class="button is-warning is-half-fullhd" target="_blank"
           href="/storage/{{$agent->contract}}">
            <ion-icon name="download-outline"></ion-icon> &nbsp;Abrir</a>
    </div>
@endif
