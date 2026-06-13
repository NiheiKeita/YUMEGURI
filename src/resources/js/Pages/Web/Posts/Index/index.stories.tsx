import { Meta, StoryObj } from '@storybook/react-vite'
import { PostIndexPage } from '.'

const meta: Meta<typeof PostIndexPage> = {
    title: 'pages/Web/Posts/Index',
    component: PostIndexPage,
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: 記事一覧
**URL**: \`/posts\`
**アクセス**: 公開（認証不要）
**Controller**: \`Web\\PostController::index\`

## 主な機能

- 公開済み記事のカード一覧（訪問日降順）
- カード: タイトル・場所名・訪問日・本文抜粋（Markdownタグ除去）
- ページネーション
- 「記事を書く」ボタンで新規作成へ
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

const makePosts = (n: number) =>
    Array.from({ length: n }, (_, i) => ({
        id: i + 1,
        user: { id: 1, name: 'けいた' },
        title: `記事タイトル ${i + 1}`,
        place_name: i % 2 === 0 ? '浅草' : null,
        lat: 35.7148,
        lng: 139.7967,
        visited_at: `2026-06-${String(14 - i).padStart(2, '0')}`,
        published_at: '2026-06-14T10:00:00',
        body: `今日は${i % 2 === 0 ? '浅草' : '上野'}を散歩しました。\n\n## 感想\n\n**良かった**です。また来たい。`,
    }))

const makePaginated = (n: number) => ({
    data: makePosts(n),
    links: { first: '/posts?page=1', last: '/posts?page=1', prev: null, next: null },
    meta: { current_page: 1, from: 1, last_page: 1, per_page: 12, to: n, total: n },
})

export const Default: Story = {
    name: '通常表示（記事あり）',
    render: () => <PostIndexPage posts={makePaginated(6)} />,
}

export const Empty: Story = {
    name: '記事なし',
    render: () => (
        <PostIndexPage
            posts={{ data: [], links: { first: '', last: '', prev: null, next: null }, meta: { current_page: 1, from: null, last_page: 1, per_page: 12, to: null, total: 0 } }}
        />
    ),
}

export const Paginated: Story = {
    name: 'ページネーションあり',
    render: () => (
        <PostIndexPage
            posts={{
                data: makePosts(12),
                links: { first: '/posts?page=1', last: '/posts?page=3', prev: null, next: '/posts?page=2' },
                meta: { current_page: 1, from: 1, last_page: 3, per_page: 12, to: 12, total: 36 },
            }}
        />
    ),
}
