
    {{-- Input para "Logo" --}}
<div class="column is-3">
    <div class="field">
        <div class="control">
            <div class="file has-name is-fullwidth">
                <label class="file-label">
                    <input class="file-input" type="file" name="logo">
                    <span class="file-cta">
                        <span class="file-label"><ion-icon name="cloud-upload-outline"></ion-icon></span>
                    </span>
                    <span class="file-name">Escolher logo...</span>
                </label>
            </div>
        </div>
    </div>
</div>

{{-- Input para "Picture" --}}
<div class="column is-3">
    <div class="field">
        <div class="control">
            <div class="file has-name is-fullwidth">
                <label class="file-label">
                    <input class="file-input" type="file" name="picture">
                    <span class="file-cta">
                            <span class="file-label"><ion-icon name="cloud-upload-outline"></ion-icon></span>
                    </span>
                    <span class="file-name">Escolher foto...</span>
                </label>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fileInputs = document.querySelectorAll('.file-input');
        fileInputs.forEach(input => {
            input.addEventListener('change', () => {
                if (input.files.length > 0) {
                    const fileName = input.closest('.file').querySelector('.file-name');
                    fileName.textContent = input.files[0].name;
                }
            });
        });
    });
</script>
