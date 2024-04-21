<div class="column is-2">
    <div class="field">
        <label for="cpf" class="label">CPF *</label>
        <div class="control has-icons-left">
            <input class="input is-rounded" name="cpf" id="cpf" type="text" required
                   @if(isset($agent)) value="{{ $agent->cpf }}"  @else value="{{ old('cpf') }}" @endif>
            <span class="icon is-small is-left">
                                <ion-icon name="document-text-outline"></ion-icon>
                            </span>
            @error('cpf')<span class="error-message">{{ $message }}</span>@enderror
        </div>
    </div>
</div>
