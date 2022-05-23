<div class="column is-2">
    <div class="field">
        <label for="ascendant" class="label">Ascendente*</label>
        <div
            class="select is-multiline is-fullwidth is-rounded @error('ascendant') is-danger @enderror">
            <select id="ascendant" name="ascendant" required>
                <option selected value="0">Sem ascendente</option>
                @foreach($agents as $agent)
                    <option value="{{$agent->id}}">{{ $agent->name }}</option>
                @endforeach
            </select>
            @error('ascendant')<span class="error-message">{{ $message }}</span>@enderror
        </div>
    </div>
</div>
