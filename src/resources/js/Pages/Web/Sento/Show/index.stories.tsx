import { Meta, StoryObj } from '@storybook/react-vite'
import { SentoShow } from '.'

const meta: Meta<typeof SentoShow> = {
    title: 'pages/Web/Sento/Show',
    component: SentoShow,
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: 銭湯詳細（雑誌風レイアウト）
**URL**: \`/sentos/{id}\`
**アクセス**: guest 可（招待制中は要 auth）
**Controller**: \`Web\\SentoController::show\`

## 主な機能

- 銭湯の基本情報（名称・住所・営業時間・料金 等）を雑誌風に表示
- 全ユーザーの感想を最新 50 件まで表示
- 「記録する / 編集」「情報を直す」ボタンで遷移

## 画面要素

- **ヘッダ**: 都道府県・市区 / 銭湯名（明朝太字）/ ふりがな
- **メインビジュアル**: 写真プレースホルダ（暖色グラデ）
- **情報テーブル** (2カラム): 住所 / 電話 / 営業時間 / 定休日 / 料金 / アクセス
- **設備チップ**: 🧴 シャンプー / 🧼 ボディソープ
- **CTA ボタン**:
  - 「記録する / 編集」 → \`/sentos/{id}/review\`
  - 「情報を直す」 → \`/sentos/{id}/propose\`
- **REVIEWS セクション**: 全ユーザーの感想カード一覧
  - ユーザー名 / ★評価 / 訪問日 / 感想 / サウナ・水風呂チップ

## 遷移先

- 訪問記録フォーム \`/sentos/{id}/review\`
- 編集提案フォーム \`/sentos/{id}/propose\`

## データ

- \`sento: SentoDetail\` — reviews 50件 + photos 30件 eager-loaded
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <SentoShow
            sento={{
                id: 1, name: '湊湯', name_kana: 'みなとゆ', prefecture: '東京都', city: '中央区',
                address: '東京都中央区湊1-6-2', lat: 35.6712, lng: 139.7763,
                phone: '03-0000-0000', hours: '15:00-23:00', closed_days: '月曜', price: 520,
                nearest_station: '八丁堀駅', walk_minutes: 5, has_shampoo: true, has_soap: true,
                status: 'open',
                reviews: [
                    {
                        id: 1, sento_id: 1, user: { id: 1, name: 'けいた' }, visited_at: '2026-04-20',
                        rating: 5, body: '炭酸泉が最高だった', has_sauna: true, sauna_temp: 90,
                        has_mizuburo: true, mizuburo_temp: 18, bath_types: ['炭酸泉'], want_revisit: true,
                        crowding: 3, best_time: '夕方',
                    },
                ],
            }}
        />
    ),
}
