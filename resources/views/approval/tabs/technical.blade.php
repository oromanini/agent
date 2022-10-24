<div class="columns" style="padding: 10px 10px">
    <h3 class="title"><img src="/img/logo/alluz-icon.png" width="30" alt="..">Vistoria</h3>
</div>
<br>

<form action="{{ route('approval.update.inspection', [$proposal->id]) }}" method="post" enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <div class="columns is-12"  style="margin: 20px 10px;">
        <div class="column is-3">
            <div class="field">
                <label for="status" class="label">Status</label>
                <div
                    class="select is-multiline is-fullwidth is-rounded  @error('status') is-danger @enderror">
                    <select id="status" name="status">
                        @foreach($inspectionStatuses as $status)
                            <option value="{{ $status }}" {{ !is_null($inspection) && $inspection->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="columns" style="margin: 20px 10px;">
        <div class="column is-12">
            <label class="label" for="observations">Observações do agente</label>
            <p>{{ $inspection->proposal->preInspection->observations }}</p>
        </div>
    </div>


    <div class="columns" style="margin: 20px 10px;">
        <div class="column is-12">
            <label class="label" for="observations">Observações da vistoria e/ou medidas necessárias</label>
            <textarea id="note" name="note" class="textarea"
                      placeholder="Adequação necessária, observações, etc...">{{ isset($inspection) ? $inspection->note : ''  }}</textarea>
        </div>
    </div>

    <div class="columns">
        <div class="column is-flex is-justify-content-center">
            <button type="submit" class="button is-large is-info">
                <ion-icon name="save-outline"></ion-icon> &nbsp; Salvar
            </button>
        </div>
    </div>
</form>

<hr>
<div class="columns is-flex is-flex-wrap-wrap" style="margin-top: 50px">
    @include('pre_inspection.images')
</div>

@include('pre_inspection.images_script')

<style>
    .pre-inspection-video {
        background-color: #a00000;
        border-radius: 50px;
        color: #fff;
        padding: 5px 10px;
    }

    .oranged {
        color: #ff6200;
        font-weight: 800;
    }

</style>
