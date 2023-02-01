<div class="column is-2">
    <div class="field">
        <label for="phone_number" class="label">Telefone*</label>
        <div class="control has-icons-left">
            <input class="input is-rounded" name="phone_number" id="phone_number" type="text"
                   @if(isset($agent)) value="{{ $agent->phone_number }}" @endif>
            <span class="icon is-small is-left">
                                <ion-icon name="call-outline"></ion-icon>
                            </span>
            @error('phone_number')<span class="error-message">{{ $message }}</span>@enderror
        </div>
    </div>
</div>
