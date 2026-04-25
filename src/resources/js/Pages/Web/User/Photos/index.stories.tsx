import { Meta, StoryObj } from '@storybook/react-vite'
import { UserPhotos } from '.'

const meta: Meta<typeof UserPhotos> = {
    title: 'pages/Web/User/Photos',
    component: UserPhotos,
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: ユーザー投稿写真一覧
**URL**: \`/users/{id}/photos\`
**アクセス**: 要 auth
**Controller**: \`Web\\UserProfileController::photos\`

## 主な機能

- 該当ユーザーが投稿した写真をグリッド表示（最新 120 件まで）
- カテゴリ（外観 / 内部 / ロッカー / その他）別の絞り込みは将来追加候補
- 写真タップで原寸表示（未実装）

## 画面要素

- セクションタイトル「{ユーザー名} の写真」
- 写真グリッド (2/3/4 カラム responsive)
  - 各セル: 正方形のサムネ + キャプション
  - URL がない場合は暖色グラデのプレースホルダ

## データ

- \`photos: SentoPhoto[]\` — 投稿日時の新しい順
- \`url\` は \`Storage::url($path)\` を accessor で展開（YUMEGURI Resource）

## 削除権限

- 写真削除は **投稿者本人 または admin** （\`SentoPhotoPolicy::delete\`）
- 現状この画面に削除 UI は無し（後続 PR）
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <UserPhotos
            profile={{ id: 1, name: 'けいた' }}
            photos={[
                { id: 1, sento_id: 1, review_id: 1, url: null, category: 'exterior', caption: '雪の湊湯' },
            ]}
        />
    ),
}
