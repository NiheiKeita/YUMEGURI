import { Meta, StoryObj } from '@storybook/react-vite'
import { PostCreatePage } from '.'

const meta: Meta<typeof PostCreatePage> = {
    title: 'pages/Web/Posts/Create',
    component: PostCreatePage,
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: 記事作成・編集
**URL**: \`/posts/create\` / \`/posts/{id}/edit\`
**アクセス**: 要認証
**Controller**: \`Web\\PostController::create\` / \`::edit\`

## 主な機能

- タイトル・場所名・緯度経度・訪問日を入力
- 「現在地を取得」ボタンでGPS位置情報を自動入力
- Markdownエディタで本文を記述
- 「画像を挿入」ボタンで画像をアップロード、カーソル位置に \`![alt](url)\` を挿入
- 「投稿する」で公開、「下書き保存」で非公開保存
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

export const NewPost: Story = {
    name: '新規作成',
    render: () => <PostCreatePage />,
}

export const EditPost: Story = {
    name: '既存記事の編集',
    render: () => (
        <PostCreatePage
            post={{
                id: 1,
                user: { id: 1, name: 'けいた' },
                title: '浅草散歩',
                place_name: '浅草',
                lat: 35.7148,
                lng: 139.7967,
                visited_at: '2026-06-14',
                published_at: '2026-06-14T10:00:00',
                body: '今日は浅草を散歩した。\n\n## 仲見世通り\n\n**人が多かった**が、楽しかった。\n\n![浅草](https://picsum.photos/400/300)',
            }}
        />
    ),
}
