import type { Preview } from "@storybook/react-vite"
import "../resources/css/app.css"
import { initialize, mswLoader } from 'msw-storybook-addon'

initialize()

const preview: Preview = {
    parameters: {
        controls: {
            matchers: {
                color: /(background|color)$/i,
                date: /Date$/i,
            },
        },
    },
    loaders: [mswLoader],
}

// Storybook/Vitest ブラウザ環境の両方で動くよう globalThis を使う
// eslint-disable-next-line @typescript-eslint/no-explicit-any
;(globalThis as any).route = (name: string) => `/${name}`

export default preview
