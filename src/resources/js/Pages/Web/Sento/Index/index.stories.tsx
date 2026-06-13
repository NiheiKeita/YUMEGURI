import { Meta, StoryObj } from '@storybook/react-vite'
import { SentoIndex } from '.'

const meta: Meta<typeof SentoIndex> = {
    title: 'pages/Web/Sento/Index',
    component: SentoIndex,
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: 銭湯一覧
**URL**: \`/sentos\`
**アクセス**: guest 可（招待制中は要 auth）
**Controller**: \`Web\\SentoController::index\`
**Service**: \`SentoListService::paginate\`

## 主な機能

- 都道府県・区市町村・訪問状態・設備タグ・営業状態でフィルタ
- ★評価 / 訪問日 / 再訪したい順 でソート
- 1ページ 20 件のページネーション
- カードタップで銭湯詳細へ遷移

## フィルタ仕様

- **都道府県**: \`東京都 / 神奈川県 / 埼玉県 / 千葉県\`
- **訪問**: \`全て / 訪問済み / 未訪問\`（要 auth）
- **ソート**: \`★評価 / 訪問日 / 再訪したい順\`
- **設備チェックボックス**: 🔥サウナ / 🧊水風呂 / 🧴シャンプー / 🧼ボディソープ
  - サウナ・水風呂は **レビュー側の has_sauna/has_mizuburo** から判定
  - シャンプー・ボディソープは **銭湯マスタの has_shampoo/has_soap**
- **bath_types** (お湯の種類): **OR セマンティクス**（チェックされたいずれかを含むレビューがある銭湯）

## 画面要素

- セクションタイトル「銭湯一覧」
- フィルタフォーム（暖色背景）
- カードグリッド (\`SentoCard\` × 1〜20)
- 0件時メッセージ

## 遷移先

- 銭湯詳細 \`/sentos/{id}\`

## データ

- \`sentos: Paginated<SentoSummary>\` — Laravel paginator 形式（data/links/meta）
- \`filters: SentoIndexFilters\` — クエリパラメータの echo back
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <SentoIndex
            filters={{}}
            sentos={{
                data: [
                    {
                        id: 1, name: '湊湯', prefecture: '東京都', city: '中央区',
                        address: '...', lat: 35, lng: 139, has_shampoo: true, has_soap: true,
                        status: 'open', avg_rating: 4.5, nearest_station: '八丁堀', walk_minutes: 5,
                    },
                ],
                links: { first: '', last: '', prev: null, next: null },
                meta: { current_page: 1, from: 1, last_page: 1, per_page: 20, to: 1, total: 1 },
            }}
        />
    ),
}
