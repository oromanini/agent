import React from 'react';
import { render } from 'react-dom';
import { createInertiaApp } from '@inertiajs/inertia-react';
import { InertiaProgress } from '@inertiajs/progress';

import '../sass/app.scss';

createInertiaApp({
    resolve: (name) => require(`./Pages/${name}`),
    setup({ el, App, props }) {
        render(<App {...props} />, el);
    },
});

InertiaProgress.init({
    color: '#1f6feb',
    showSpinner: true,
});
