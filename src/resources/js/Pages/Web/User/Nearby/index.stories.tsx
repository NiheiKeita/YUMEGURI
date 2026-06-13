import { Meta, StoryObj } from '@storybook/react-vite'
import { UserNearby } from '.'

const meta: Meta<typeof UserNearby> = {
    title: 'pages/Web/User/Nearby',
    component: UserNearby,
    tags: ['autodocs'],
    parameters: {
        docs: {
            description: {
                component: `
**画面名**: ユーザー視点の「近くの未訪問」（自分のページのみ）
**URL**: \`/users/{id}/nearby\`
**アクセス**: 要 auth、**自分のページのみ**（他人のページは 404）
**Controller**: \`Web\\UserProfileController::nearby\`
**Service**: \`NearbySentoService::findUnvisited\`

## 主な機能

- 自分のプロフィールから「近くの未訪問」へショートカット
- ロジックは \`/nearby\` と同じだが、URL がプロフィール傘下にあるためメニューから自然に辿れる

## 「自分のみ」の判定

\`\`\`php
$viewer = $request->user();
abort_unless($viewer instanceof User && $viewer->id === $user->id, 404);
\`\`\`

- 403 でなく 404 を返すことで「他人のページ」と気付かせない
- Policy ではなく Controller 内に閉じている（リソース所有者判定は if 1行のほうが意図が明示的）

## 画面要素

- セクションタイトル「近くの未訪問銭湯」+ プロフィール名
- 現在地座標表示
- カードグリッド (\`SentoCard\`)
        `,
            },
        },
    },
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <UserNearby
            profile={{ id: 1, name: 'けいた' }}
            origin={{ lat: 35.68, lng: 139.77 }}
            sentos={[]}
        />
    ),
}
