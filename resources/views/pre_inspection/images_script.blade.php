<script>
    const croqui = document.querySelector('#croqui input[type=file]');
    const pattern = document.querySelector('#pattern input[type=file]');
    const pattern_circuit_break = document.querySelector('#pattern_circuit_break input[type=file]');
    const roof = document.querySelector('#roof input[type=file]');
    const switchboard = document.querySelector('#switchboard input[type=file]');
    const post = document.querySelector('#post input[type=file]');
    const compass = document.querySelector('#compass input[type=file]');
    const property_fax = document.querySelector('#property_fax input[type=file]');
    const open_pattern = document.querySelector('#open_pattern input[type=file]');
    const meter = document.querySelector('#meter input[type=file]');
    const roof_structure = document.querySelector('#roof_structure input[type=file]');
    const inverter_local = document.querySelector('#inverter_local input[type=file]');

    open_pattern.onchange = () => {
        if (open_pattern.files.length > 0) {
            const fileName = document.querySelector('#open_pattern .file-name');
            fileName.textContent = open_pattern.files[0].name;
        }
    }

    meter.onchange = () => {
        if (meter.files.length > 0) {
            const fileName = document.querySelector('#meter .file-name');
            fileName.textContent = meter.files[0].name;
        }
    }

    roof_structure.onchange = () => {
        if (roof_structure.files.length > 0) {
            const fileName = document.querySelector('#roof_structure .file-name');
            fileName.textContent = roof_structure.files[0].name;
        }
    }

    inverter_local.onchange = () => {
        if (inverter_local.files.length > 0) {
            const fileName = document.querySelector('#inverter_local .file-name');
            fileName.textContent = inverter_local.files[0].name;
        }
    }

    croqui.onchange = () => {
        if (croqui.files.length > 0) {
            const fileName = document.querySelector('#croqui .file-name');
            fileName.textContent = croqui.files[0].name;
        }
    }

    pattern.onchange = () => {
        if (pattern.files.length > 0) {
            const fileName = document.querySelector('#pattern .file-name');
            fileName.textContent = pattern.files[0].name;
        }
    }

    pattern_circuit_break.onchange = () => {
        if (pattern_circuit_break.files.length > 0) {
            const fileName = document.querySelector('#pattern_circuit_break .file-name');
            fileName.textContent = pattern_circuit_break.files[0].name;
        }
    }

    roof.onchange = () => {
        if (roof.files.length > 0) {
            const fileName = document.querySelector('#roof .file-name');
            fileName.textContent = roof.files[0].name;
        }
    }

    switchboard.onchange = () => {
        if (switchboard.files.length > 0) {
            const fileName = document.querySelector('#switchboard .file-name');
            fileName.textContent = switchboard.files[0].name;
        }
    }

    post.onchange = () => {
        if (post.files.length > 0) {
            const fileName = document.querySelector('#post .file-name');
            fileName.textContent = post.files[0].name;
        }
    }

    compass.onchange = () => {
        if (compass.files.length > 0) {
            const fileName = document.querySelector('#compass .file-name');
            fileName.textContent = compass.files[0].name;
        }
    }

    property_fax.onchange = () => {
        if (property_fax.files.length > 0) {
            const fileName = document.querySelector('#property_fax .file-name');
            fileName.textContent = property_fax.files[0].name;
        }
    }

</script>
