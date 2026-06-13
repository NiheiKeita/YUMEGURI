import { Meta, StoryObj } from '@storybook/react-vite'
import { Nearby } from '.'

const meta: Meta<typeof Nearby> = {
    title: 'pages/Web/Nearby',
    component: Nearby,
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: 近くの未訪問銭湯
**URL**: \`/nearby\` （\`?lat=&lng=\`）
**アクセス**: 要 auth
**Controller**: \`Web\\NearbyController::index\`
**Service**: \`NearbySentoService::findUnvisited\`

## 主な機能

- ブラウザの位置情報から自動で現在地を取得
- 現在地から距離順に未訪問銭湯を 20 件表示
- 距離 (km)・最寄り駅・徒歩分数をカードに表示

## 位置情報ハンドリング

- \`navigator.geolocation.getCurrentPosition\` を useEffect 内で呼ぶ
- **同セッションで一度取得したら再要求しない** （\`sessionStorage\` で抑制、リロード毎にダイアログを出さない）
- 失敗時のフォールバック:
  - PERMISSION_DENIED: 「位置情報が許可されていません」と表示し、デフォルト座標（東京駅）で表示
  - 未対応ブラウザ: 「このブラウザは位置情報に対応していません」
  - その他エラー: 「位置情報の取得に失敗しました」
- timeout: 8s, maximumAge: 60s

## 距離計算

- SQL での Haversine 近似式（小規模想定）
- パフォーマンスが必要になったら空間インデックスへ移行

## 画面要素

- セクションタイトル「近くの未訪問銭湯」
- 現在地座標表示
- ステータス警告バナー（permission denied 等のとき）
- カードグリッド (\`SentoCard\`)
- 0件時メッセージ

## 既知の制限

- 訪問済みフィルタは viewer 視点のみ。広域から探すと候補が少なくなる
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <Nearby
            origin={{ lat: 35.68, lng: 139.77 }}
            sentos={[
                {
                    id: 1, name: '湊湯', prefecture: '東京都', address: '...',
                    lat: 35.67, lng: 139.77, has_shampoo: true, has_soap: true, status: 'open',
                    distance_km: 0.42, nearest_station: '八丁堀', walk_minutes: 5,
                },
            ]}
        />
    ),
}
