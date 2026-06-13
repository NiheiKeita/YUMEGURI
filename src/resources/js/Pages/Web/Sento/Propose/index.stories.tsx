import { Meta, StoryObj } from '@storybook/react-vite'
import { SentoPropose } from '.'

const meta: Meta<typeof SentoPropose> = {
    title: 'pages/Web/Sento/Propose',
    component: SentoPropose,
    tags: ['autodocs', '!test'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: 銭湯情報の編集提案フォーム
**URL**: \`/sentos/{id}/propose\`
**アクセス**: 要 auth (member 以上)
**Controller**: \`Web\\SentoEditProposalController::create / store\`
**Service**: \`ProposalSubmitService::execute\`

## 主な機能

- 銭湯マスタ情報の修正案を admin に送信する
- 直接編集ではなく **承認待ちキュー** に積まれる仕組み（status=pending）
- 編集理由を任意で添える

## 提案可能な項目

allowlist 化されており、機密項目（lat/lng/prefecture/status 等）は提案不可:

- 銭湯名 / 住所 / 電話 / 営業時間 / 定休日 / 料金 / 最寄り駅 / 徒歩分数

## 提案フロー

\`\`\`
member: 提案送信 → status=pending で sento_edit_proposals に保存
                    ↓
admin: /admin/proposals で確認 → 承認 / 却下
                    ↓
承認時: changes が sentos に反映 + is_manually_updated=true
\`\`\`

## 画面要素

- 各項目の input フィールド（placeholder に現在値を表示）
- 「変更の根拠・理由」 textarea（500 文字まで）
- 「提案を送信」ボタン

## 注意

- このページは Inertia useForm に依存するため Storybook test (\`!test\`) で除外
- \`SentoEditProposalStoreRequest\` で許可外キーは黙って捨てる
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <SentoPropose
            sento={{
                id: 1, name: '湊湯', prefecture: '東京都', address: '東京都中央区湊1-6-2',
                lat: 35, lng: 139, has_shampoo: true, has_soap: true, status: 'open',
            }}
        />
    ),
}
