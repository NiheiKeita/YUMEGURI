import './bootstrap'
import '../css/app.css'

import { createRoot } from 'react-dom/client'
import { createInertiaApp } from '@inertiajs/react'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import type { ComponentType } from 'react'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'
const pages = import.meta.glob<{ default: ComponentType }>('./Pages/**/*.tsx')

createInertiaApp({
    // title: (title) => `${title} - ${appName}`,
    title: (title) => `${title} ${appName}`,
    resolve: (name) =>
        resolvePageComponent<{ default: ComponentType }>(
            `./Pages/${name}/index.tsx`,
            pages,
        ).then(module => module.default),
    setup({ el, App, props }) {
        const root = createRoot(el)

        root.render(<App {...props} />)
    },
    progress: {
        color: '#4B5563',
    },
})
