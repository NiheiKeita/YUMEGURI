import { Meta, StoryObj } from '@storybook/react-vite'
import { SentoEdit } from '.'

const meta: Meta<typeof SentoEdit> = {
    title: 'pages/Web/Sento/Edit',
    component: SentoEdit,
    tags: ['autodocs', '!test'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: 銭湯情報の直接編集（admin 専用）
**URL**: \`GET /sentos/{id}/edit\`, \`PATCH /sentos/{id}\`
**アクセス**: 要 auth + admin
**Controller**: \`Web\\SentoController::edit / update\`
**Validation**: \`SentoUpdateRequest\`
**Middleware**: \`auth\` + \`admin\` (\`EnsureAdmin\`)

## 主な機能

- 提案フローを介さず admin が直接マスタを上書きする
- 保存時に \`is_manually_updated=true\` がセットされ、再スクレイピングで上書きされなくなる

## 編集可能な項目

提案フォームと違い、**lat/lng/prefecture/status 含む全項目** を編集できる:

- 銭湯名 / 住所 / 営業時間 / 定休日 / 料金 / 営業状態
- 設備（シャンプー / ボディソープ）

## 保存時の副作用

1. \`changes\` を fill して save
2. \`is_manually_updated = true\`
3. \`info_updated_at = today\`

## 注意

- 二重認可: \`admin\` middleware + \`SentoUpdateRequest::authorize\` で防御
- このページは Inertia useForm に依存するため Storybook test (\`!test\`) で除外
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <SentoEdit
            sento={{
                id: 1, name: '湊湯', prefecture: '東京都', address: '...', lat: 35, lng: 139,
                has_shampoo: true, has_soap: true, status: 'open',
            }}
        />
    ),
}
