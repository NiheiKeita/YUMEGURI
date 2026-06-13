import { Meta, StoryObj } from '@storybook/react-vite'
import { Top } from '.'

const meta: Meta<typeof Top> = {
    title: 'pages/Web/Top',
    component: Top,
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: トップ
**URL**: \`/\`
**Controller**: \`Web\\TopController::index\`

## 主な機能

- メンバー一覧（記事数バッジ付き）
- 最新ブログ記事12件
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

const sampleUsers = [
    { id: 1, name: 'けいた', posts_count: 5 },
    { id: 2, name: 'たろう', posts_count: 2 },
    { id: 3, name: 'はなこ', posts_count: 0 },
]

const samplePosts = Array.from({ length: 6 }, (_, i) => ({
    id: i + 1,
    user: { id: 1, name: 'けいた' },
    title: `記事タイトル ${i + 1}`,
    place_name: i % 2 === 0 ? '浅草' : null,
    lat: null,
    lng: null,
    visited_at: `2026-06-${String(14 - i).padStart(2, '0')}`,
    published_at: '2026-06-14T10:00:00',
    body: `今日は**楽しい**一日でした。\n\n- ポイント1\n- ポイント2`,
}))

export const Default: Story = {
    name: '通常表示',
    render: () => <Top users={sampleUsers} latestPosts={samplePosts} />,
}

export const Empty: Story = {
    name: '記事なし',
    render: () => <Top users={[{ id: 1, name: 'けいた', posts_count: 0 }]} latestPosts={[]} />,
}
