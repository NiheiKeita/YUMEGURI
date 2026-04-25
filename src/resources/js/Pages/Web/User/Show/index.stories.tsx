import { Meta, StoryObj } from '@storybook/react-vite'
import { UserShow } from '.'

const meta: Meta<typeof UserShow> = {
    title: 'pages/Web/User/Show',
    component: UserShow,
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: ユーザープロフィール
**URL**: \`/users/{id}\`
**アクセス**: 要 auth
**Controller**: \`Web\\UserProfileController::show\`

## 主な機能

- ユーザーの訪問統計（訪問数 / 都道府県数 / 区市町村数）
- 訪問済み銭湯の一覧（最新 60 件）
- マップ・写真・近くの未訪問へのナビゲーション

## 自分 vs 友達の違い

| 機能 | 自分のページ | 友達のページ |
|---|:-:|:-:|
| 訪問済み一覧 | ✅ | ✅ 閲覧のみ |
| ユーザーマップ | ✅ | ✅ |
| 近くの未訪問銭湯リンク | ✅ | ✅（リンクは出るが、実体は 404） |
| 投稿写真 | ✅ | ✅ |

## 画面要素

- **プロフィールヘッダ**（暖色グラデ）: ロール表示 / ユーザー名 / 統計 (訪問・都道府県・区市町村)
- **ナビゲーションチップ**: 🗺マップ / 📸写真 / 📍近くの未訪問
- **VISITED セクション**: 訪問済み銭湯リスト
  - 銭湯名 / ★評価 / 訪問日 / 「♨ また行きたい」マーク

## 統計の算出

集計クエリで取得（全レビューを load しない）:
- \`visited_count\` = レビュー数
- \`prefecture_count\` = sentos.prefecture の distinct 数
- \`city_count\` = sentos.city の distinct 数（null 除外）

## 遷移先

- 銭湯詳細 \`/sentos/{id}\`
- ユーザーマップ \`/users/{id}/map\`
- ユーザー写真一覧 \`/users/{id}/photos\`
- 近くの未訪問 \`/users/{id}/nearby\`
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <UserShow
            profile={{ id: 1, name: 'けいた' }}
            stats={{ visited_count: 12, prefecture_count: 3, city_count: 7 }}
            reviews={[
                {
                    id: 1, sento_id: 1, visited_at: '2026-04-20', rating: 5,
                    body: '', has_sauna: true, sauna_temp: 90, has_mizuburo: true, mizuburo_temp: 18,
                    bath_types: [], want_revisit: true, crowding: 3, best_time: '夕方',
                    sento: { id: 1, name: '湊湯', prefecture: '東京都', address: '', lat: 0, lng: 0,
                        has_shampoo: true, has_soap: true, status: 'open' },
                },
            ]}
        />
    ),
}
