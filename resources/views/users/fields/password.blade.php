<!-- Ionicons (adicione no <head> ou antes do fechamento do <body>) -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<div class="column is-2">
    <div class="field">
        <label for="password" class="label">Senha*</label>
        <div class="control has-icons-left has-icons-right">
            <input class="input is-rounded" name="password" id="password" type="password"
                   @if(isset($agent)) value="{{ $agent->password }}" @endif>
            <span class="icon is-small is-left">
                <ion-icon name="key-outline"></ion-icon>
            </span>

            @error('password')<span class="error-message">{{ $message }}</span>@enderror
        </div>
    </div>
</div>
