import { Meta, StoryObj } from '@storybook/react-vite'
import { Top } from '.'

const meta: Meta<typeof Top> = {
    title: 'pages/Web/Top',
    component: Top,
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: トップ（雑誌風・最新訪問カード）
**URL**: \`/\`
**アクセス**: guest 可（招待制中は要 auth）
**Controller**: \`Web\\TopController::index\`

## 主な機能

- ヒーローエリアでサイト名・コンセプトを提示
- 最新訪問記録を 12 件まで雑誌風にカード一覧
- カードタップで該当銭湯詳細へ遷移

## 画面要素

- **Hero**: サイト名「YUMEGURI」/ 対象エリアラベル / コンセプトコピー
- **LATEST セクション**: 訪問記録カード × 最大12件
  - 写真プレースホルダ / 銭湯名 / ★評価 / 訪問日 / 感想抜粋 (3行)
- 記録が0件のときは「まだ訪問記録がありません。」メッセージ

## 遷移先

- 銭湯詳細 \`/sentos/{sento_id}\`

## データ

- \`latestReviews: SentoReview[]\` — \`SentoReviewResource\` 経由で sento/user/photos eager-loaded
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <Top
            latestReviews={[
                {
                    id: 1,
                    sento_id: 1,
                    visited_at: '2026-04-20',
                    rating: 5,
                    body: '炭酸泉が最高だった。サウナも90度で締まりがある。',
                    has_sauna: true,
                    sauna_temp: 90,
                    has_mizuburo: true,
                    mizuburo_temp: 18,
                    bath_types: ['炭酸泉'],
                    want_revisit: true,
                    crowding: 3,
                    best_time: '夕方',
                    sento: {
                        id: 1, name: '湊湯', prefecture: '東京都', address: '中央区',
                        lat: 35, lng: 139, has_shampoo: true, has_soap: true, status: 'open',
                    },
                },
            ]}
        />
    ),
}
export const Empty: Story = { render: () => <Top latestReviews={[]} /> }
