<div class="column is-2">
    <div class="field">
        <label for="password" class="label">Senha*</label>
        <div class="control has-icons-left">
            <input class="input is-rounded" name="password" id="password" type="password"
                   @if(isset($agent)) value="{{ $agent->password }}" @endif>
            <span class="icon is-small is-left">
                                <ion-icon name="key-outline"></ion-icon>
                            </span>
            @error('password')<span class="error-message">{{ $message }}</span>@enderror
        </div>
    </div>
</div>
