<script>
    const panels = document.querySelector('#panels input[type=file]');
    const general = document.querySelector('#general input[type=file]');
    const inverterTag = document.querySelector('#inverterTag input[type=file]');
    const datalogger = document.querySelector('#datalogger input[type=file]');
    const patternWithPlate = document.querySelector('#patternWithPlate input[type=file]');
    const ca_tension = document.querySelector('#ca_tension input[type=file]');
    const cc_tension = document.querySelector('#cc_tension input[type=file]');
    const ca_current = document.querySelector('#ca_current input[type=file]');
    const cc_current = document.querySelector('#cc_current input[type=file]');
    const grounding = document.querySelector('#grounding input[type=file]');
    const dps = document.querySelector('#dps input[type=file]');

    panels.onchange = () => {
        if (panels.files.length > 0) {
            const fileName = document.querySelector('#panels .file-name');
            fileName.textContent = panels.files[0].name;
        }
    }

    general.onchange = () => {
        if (general.files.length > 0) {
            const fileName = document.querySelector('#general .file-name');
            fileName.textContent = general.files[0].name;
        }
    }

    inverterTag.onchange = () => {
        if (inverterTag.files.length > 0) {
            const fileName = document.querySelector('#inverterTag .file-name');
            fileName.textContent = inverterTag.files[0].name;
        }
    }

    datalogger.onchange = () => {
        if (datalogger.files.length > 0) {
            const fileName = document.querySelector('#datalogger .file-name');
            fileName.textContent = datalogger.files[0].name;
        }
    }

    patternWithPlate.onchange = () => {
        if (patternWithPlate.files.length > 0) {
            const fileName = document.querySelector('#patternWithPlate .file-name');
            fileName.textContent = patternWithPlate.files[0].name;
        }
    }

    ca_tension.onchange = () => {
        if (ca_tension.files.length > 0) {
            const fileName = document.querySelector('#ca_tension .file-name');
            fileName.textContent = ca_tension.files[0].name;
        }
    }

    cc_tension.onchange = () => {
        if (cc_tension.files.length > 0) {
            const fileName = document.querySelector('#cc_tension .file-name');
            fileName.textContent = cc_tension.files[0].name;
        }
    }

    ca_current.onchange = () => {
        if (ca_current.files.length > 0) {
            const fileName = document.querySelector('#ca_current .file-name');
            fileName.textContent = ca_current.files[0].name;
        }
    }

    cc_current.onchange = () => {
        if (cc_current.files.length > 0) {
            const fileName = document.querySelector('#cc_current .file-name');
            fileName.textContent = cc_current.files[0].name;
        }
    }

    grounding.onchange = () => {
        if (grounding.files.length > 0) {
            const fileName = document.querySelector('#grounding .file-name');
            fileName.textContent = grounding.files[0].name;
        }
    }

    dps.onchange = () => {
        if (dps.files.length > 0) {
            const fileName = document.querySelector('#dps .file-name');
            fileName.textContent = dps.files[0].name;
        }
    }

</script>
