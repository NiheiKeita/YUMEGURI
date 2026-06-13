import { Meta, StoryObj } from '@storybook/react-vite'
import { PostShowPage } from '.'

const meta: Meta<typeof PostShowPage> = {
    title: 'pages/Web/Posts/Show',
    component: PostShowPage,
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: 記事詳細
**URL**: \`/posts/{id}\`
**アクセス**: 公開（認証不要）
**Controller**: \`Web\\PostController::show\`

## 主な機能

- タイトル・訪問日・場所名を表示
- 位置情報がある場合は「地図で見る」リンク（Google Maps）
- MarkdownをHTMLとしてレンダリング（markdown-to-jsx）
- 記事の投稿者本人は編集・削除ボタンが表示される
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

const sampleBody = `
今日は浅草を散歩した。天気が良くて最高だった。

## 仲見世通り

**人が多かった**が、裏路地は静かで良かった。

![仲見世通り](https://picsum.photos/400/300)

帰りに雷門の前で写真を撮った。また来たい。

- 雷門
- 仲見世通り
- 浅草寺
`

const samplePost = {
    id: 1,
    user: { id: 1, name: 'けいた' },
    title: '浅草散歩',
    place_name: '浅草',
    lat: 35.7148,
    lng: 139.7967,
    visited_at: '2026-06-14',
    published_at: '2026-06-14T10:00:00',
    body: sampleBody,
}

export const Default: Story = {
    name: '通常表示（閲覧者）',
    render: () => <PostShowPage post={samplePost} canEdit={false} />,
}

export const WithEditButton: Story = {
    name: '投稿者本人（編集ボタンあり）',
    render: () => <PostShowPage post={samplePost} canEdit={true} />,
}

export const NoLocation: Story = {
    name: '位置情報なし',
    render: () => (
        <PostShowPage
            post={{ ...samplePost, lat: null, lng: null, place_name: null }}
            canEdit={false}
        />
    ),
}
