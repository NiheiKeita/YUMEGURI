import type { Preview } from "@storybook/react-vite"
import "../resources/css/app.css"
import { initialize, mswLoader } from 'msw-storybook-addon'

// MSW worker は実ブラウザ（Service Worker あり）でのみ起動する。
// Vitest browser project は setProjectAnnotations 経由で preview を Node 側でも
// 一度 import するため、無条件で initialize() を呼ぶと
//   - msw の package.json exports に "node": null が指定されている
//   - そのため Node 環境で setupWorker が undefined を返す
//   - "Cannot set properties of undefined (setting 'activationPromise')" で落ちる
// → window が存在する実ブラウザ環境のみで起動するようガードする。
if (typeof window !== 'undefined' && typeof navigator !== 'undefined' && 'serviceWorker' in navigator) {
    initialize({ onUnhandledRequest: 'bypass' })
}

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
