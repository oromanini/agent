<div class="column is-2">
    <div class="field">
        <label for="cnpj" class="label">CNPJ</label>
        <div class="control has-icons-left">
            <input class="input is-rounded" name="cnpj" id="cnpj" type="text"
                   @if(isset($agent)) value="{{ $agent->cnpj }}"  @else value="{{ old('cnpj') }}" @endif>
            <span class="icon is-small is-left">
                                <ion-icon name="briefcase-outline"></ion-icon>
                            </span>
            @error('cnpj')<span class="error-message">{{ $message }}</span>@enderror
        </div>
    </div>
</div>
