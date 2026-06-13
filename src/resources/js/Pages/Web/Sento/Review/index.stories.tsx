import { Meta, StoryObj } from '@storybook/react-vite'
import { SentoReviewPage } from '.'

const meta: Meta<typeof SentoReviewPage> = {
    title: 'pages/Web/Sento/Review',
    component: SentoReviewPage,
    tags: ['autodocs', '!test'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: 訪問記録フォーム
**URL**: \`/sentos/{id}/review\`
**アクセス**: 要 auth (member 以上)
**Controller**: \`Web\\SentoReviewController::create / store\`
**Service**: \`ReviewUpsertService::execute\`

## 主な機能

- 同じ銭湯への過去の訪問記録があれば編集モード（同日付は上書き）
- 訪問日・★評価・感想・サウナ温度・水風呂温度・お湯の種類・混雑度・再訪希望を入力

## フォーム項目

| 項目 | 入力形式 | 必須 |
|---|---|---|
| 訪問日 | \`<input type=date>\` | ✅ |
| ★評価 (1-5) | range スライダー | ✅ |
| 感想 | textarea (5000 chars) | — |
| 🔥 サウナあり | checkbox | — |
| サウナ温度℃ | number (30-150) | — |
| 🧊 水風呂あり | checkbox | — |
| 水風呂温度℃ | number (0-40) | — |
| お湯の種類 | チップマルチ選択（炭酸泉/薬湯/シルク/電気/露天/ジェット） | — |
| 混雑度 (1-5) | number | — |
| おすすめ時間帯 | text | — |
| また行きたい | checkbox | — |

## 保存後

- 302 → 銭湯詳細 (\`/sentos/{id}\`) + flash「記録を保存しました」

## バリデーション失敗時

- 422 + \`errors\` を Inertia useForm 経由で各フィールド下に表示

## hooks

- \`useReviewForm(sentoId, initial)\` - フォーム state と submit を集約

## 注意

- このページは Inertia useForm に依存するため Storybook test (\`!test\`) で除外
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

export const Empty: Story = {
    render: () => (
        <SentoReviewPage
            review={null}
            sento={{
                id: 1, name: '湊湯', prefecture: '東京都', address: '...',
                lat: 35, lng: 139, has_shampoo: true, has_soap: true, status: 'open',
            }}
        />
    ),
}
