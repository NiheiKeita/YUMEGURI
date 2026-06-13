import { Meta, StoryObj } from '@storybook/react-vite'
import { UserMap } from '.'

const meta: Meta<typeof UserMap> = {
    title: 'pages/Web/User/Map',
    component: UserMap,
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: ユーザーマップ（足跡マップ）
**URL**: \`/users/{id}/map\`
**アクセス**: 要 auth
**Controller**: \`Web\\UserProfileController::map\`

## 主な機能

- 指定ユーザーが訪問済みの銭湯だけをピン表示（足跡マップ）
- 未訪問は表示しない（全体の銭湯マップ \`/map\` とは異なる）
- ピンタップで銭湯名・訪問日・★評価をポップアップ

## 画面要素

- 「{ユーザー名} の足跡」セクションタイトル
- 訪問済みのみのマップ（全ピン \`visited=true\` 状態）

## データ

- \`pins: Pin[]\` — 該当ユーザーの全 SentoReview から sento を pluck
- \`lat=0, lng=0\` のものは座標欠損として除外
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <UserMap
            profile={{ id: 1, name: 'けいた' }}
            pins={[{ id: 1, name: '湊湯', lat: 35.67, lng: 139.77, visited_at: '2026-04-20', rating: 5 }]}
        />
    ),
}
