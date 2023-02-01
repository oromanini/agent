<div class="column is-2">
    <div class="field">
        <label for="name" class="label">Nome*</label>
        <div class="control has-icons-left">
            <input class="input is-rounded" name="name" id="name" type="text"
                   @if(isset($agent)) value="{{ $agent->name }}" @else value="{{ old('name') }}" @endif>
            <span class="icon is-small is-left">
                                <ion-icon name="person-outline"></ion-icon></span>
            @error('name')<span class="error-message">{{ $message }}</span>@enderror
        </div>
    </div>
</div>
