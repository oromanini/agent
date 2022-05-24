<div class="column is-3">
    <div class="field">
        <label for="email" class="label">Email*</label>
        <div class="control has-icons-left">
            <input class="input is-rounded" name="email" id="email" type="email"
                   @if(isset($agent)) value="{{ $agent->email }}"  @else value="{{ old('email') }}" @endif>
            <span class="icon is-small is-left">
                                <ion-icon name="mail-outline"></ion-icon>
                            </span>
            @error('email')<span class="error-message">{{ $message }}</span>@enderror
        </div>
    </div>
</div>
