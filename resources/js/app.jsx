import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import '@fortawesome/fontawesome-free/css/all.css';

import { Ziggy } from './ziggy';
import { route } from 'ziggy-js';

window.route = (name, params, absolute = false) =>
    route(name, params, absolute, Ziggy);

createInertiaApp({
    resolve: async (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx');
        const page = await pages[`./Pages/${name}.jsx`]();
        return page.default;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
