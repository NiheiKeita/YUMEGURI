import { Meta, StoryObj } from '@storybook/react-vite'
import { Map } from '.'

const meta: Meta<typeof Map> = {
    title: 'pages/Web/Map',
    component: Map,
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: 銭湯マップ
**URL**: \`/map\` （\`?prefecture=東京都\` で絞り込み可）
**アクセス**: guest 可（招待制中は要 auth）
**Controller**: \`Web\\MapController::index\`

## 主な機能

- 全銭湯（営業中・座標あり）をピン表示
- 訪問済み（塗りつぶし）/ 未訪問（アウトライン）で色分け
- ピンタップで銭湯名・訪問状況・★評価をポップアップ表示
- 現在地表示・現在地から近い銭湯をハイライト

## 画面要素

- セクションタイトル「銭湯マップ」+ 凡例
- マップ本体（\`MapView\` コンポーネント）
  - 現状はプレースホルダ実装。Google Maps / Mapbox 差し込み予定

## データ

- \`pins: Pin[]\` — 最大 2000 件、operating かつ \`lat/lng\` あり
- \`visited\` フラグはログインユーザの \`sentoReviews\` から算出

## 既知の制限

- ピン数が 1000 を超える場合は別 API endpoint からの fetch に分離予定
- マップ実装は **TODO**（差し替え地点は MapView 内に明示）
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <Map
            pins={[
                { id: 1, name: '湊湯', lat: 35.67, lng: 139.77, visited: true },
                { id: 2, name: '清水湯', lat: 35.66, lng: 139.7, visited: false },
            ]}
        />
    ),
}
