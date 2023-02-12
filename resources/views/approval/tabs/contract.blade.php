<div style="padding: 30px">
    <div class="columns">
        <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt="..">Contrato Compra e venda </h3>
    </div>
    <br>
    <form action="{{ route('approval.update.contract', [$proposal->id]) }}" method="post" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        <div class="columns">
            {{--        @if--}}
            <div class="column">
                <label for="document" class="label">Contrato em PDF</label>
                <div class="file has-name">
                    <label class="file-label">
                        <input class="file-input" type="file" name="file" id="file">
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
            </div>
            <div class="column">
                <label for="signed_document" class="label">Contrato assinado</label>
                <div class="file has-name">
                    <label class="file-label">
                        <input class="file-input" type="file" name="signed_file" id="signed_file">
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
            </div>
            <div class="column">
                <div class="field">
                    <label for="status" class="label">Status</label>
                    <div
                        class="select is-multiline is-fullwidth is-rounded  @error('status') is-danger @enderror">
                        <select id="status" name="status_id">
                            @foreach($contractStatuses as $status)
                                <option value="{{ $status->id }}" {{ isset($contract) && $status->id == $contract->status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                        @error('status')<span class="error-message">{{ $message }}</span>@enderror

                    </div>
                </div>
            </div>
        </div>
        <div class="columns">
            <div class="column">
                <button type="submit" class="button is-primary is-large"><ion-icon name="save-outline"></ion-icon> &nbsp;Salvar</button>
            </div>
        </div>
    </form>
    <hr>
    <div class="columns">
        <div class="column">
            @if(isset($contract->signed_file))
                <embed style="width: 100%; height: 900px" src="/{{ str_replace('public', 'storage', $contract->signed_file) }}" alt="pdf" />
            @elseif(isset($contract->file))
                <embed style="width: 100%; height: 900px" src="/{{ str_replace('public', 'storage', $contract->file) }}" alt="pdf" />
            @else
                <h4 class="title is-4">O contrato ainda não foi anexado.</h4>
            @endif
        </div>
    </div>
</div>

<script>
    //contract
    const file = document.querySelector('#file input[type=file]');
    const signed_file = document.querySelector('#signed_file input[type=file]');

    file.onchange = () => {
        if (file.files.length > 0) {
            const fileName = document.querySelector('#file .file-name');
            fileName.textContent = file.files[0].name;
        }
    }

    signed_file.onchange = () => {
        if (signed_file.files.length > 0) {
            const fileName = document.querySelector('#signed_file .file-name');
            fileName.textContent = signed_file.files[0].name;
        }
    }
</script>
