import { Meta, StoryObj } from '@storybook/react-vite'
import { ProposalsIndex } from '.'

const meta: Meta<typeof ProposalsIndex> = {
    title: 'pages/Admin/Proposals/Index',
    component: ProposalsIndex,
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: 編集提案レビュー（admin 専用）
**URL**: \`/admin/proposals\`
**アクセス**: 要 auth + admin
**Controller**: \`Admin\\ProposalController::index / approve / reject\`
**Service**: \`ProposalReviewService::approve / reject\`
**Middleware**: \`auth\` + \`admin\`

## 主な機能

- 提案一覧（最新順 30 件ページネーション）
- 各提案の差分（\`changes\` JSON）と理由を確認
- 「承認して反映」「却下」のアクション

## 提案カードの内容

- 銭湯名（ない場合は \`銭湯 #{id}\`）
- ステータスバッジ（保留中 / 承認済 / 却下）
- 提案者・作成日時
- 理由（任意）
- 変更内容の JSON プレビュー
- アクションボタン（**status=pending のときのみ表示**）

## 承認時の副作用

\`\`\`
ProposalReviewService::approve（DB transaction + lockForUpdate）
  ↓
1. Sento.changes を fill して save
2. Sento.is_manually_updated = true
3. Sento.info_updated_at = today
4. SentoEditProposal.status = approved
5. reviewed_by, reviewed_at を記録
\`\`\`

## 却下時の副作用

- Sento は変更されない
- SentoEditProposal.status = rejected のみ更新

## 二重承認防止

- すでに \`pending\` でない提案を再度 approve/reject すると \`RuntimeException\`
- DB ロックでレース防止（同時に2人の admin が承認しないように）

## ステータスバッジ色

| ステータス | 色 |
|---|---|
| 保留中 | 暖色 (amber) |
| 承認済 | 緑 (emerald) |
| 却下 | 灰 |
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <ProposalsIndex
            proposals={{
                data: [
                    {
                        id: 1, sento_id: 1, sento: { id: 1, name: '湊湯' },
                        proposer: { id: 2, name: 'ともだち' },
                        changes: { hours: '14:00-23:30' },
                        reason: '営業時間が変わったみたい',
                        status: 'pending',
                        reviewed_at: null,
                        created_at: '2026-04-20T10:00:00+09:00',
                    },
                ],
                links: { first: '', last: '', prev: null, next: null },
                meta: { current_page: 1, from: 1, last_page: 1, per_page: 30, to: 1, total: 1 },
            }}
        />
    ),
}
