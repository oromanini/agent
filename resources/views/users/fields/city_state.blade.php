<div class="column is-2">
    <div class="field">
        <label for="state" class="label">Estado*</label>
        <div
            class="select is-multiline is-fullwidth is-rounded @error('state') is-danger @enderror">
            <select id="state" name="state" required>
                @foreach($states as $state)
                    <option @if(isset($client) && $address->city->state->id == $state->id) selected
                            @endif value="{{ $state->id }}">{{ $state->name }}</option>
                @endforeach
            </select>
            @error('state')<span class="error-message">{{ $message }}</span>@enderror
        </div>
    </div>
    @error('state')<span class="error-message">{{ $message }}</span>@enderror
</div>
<div class="column is-2">
    <div class="field">
        <label for="city" class="label">Cidade*</label>
        <div
            class="select is-multiline is-fullwidth is-rounded @error('city') is-danger @enderror">
            <select id="city" name="city" required>
                <option selected>Selecione...</option>
            </select>
            @error('city')<span class="error-message">{{ $message }}</span>@enderror

        </div>
    </div>
    @error('city')<span class="error-message">{{ $message }}</span>@enderror

</div>
