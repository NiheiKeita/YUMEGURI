---
name: create-component
description: 新しい React コンポーネント（またはページ）をこのリポジトリの規約に従ったディレクトリ型（index.tsx + index.stories.tsx [+ hooks.ts]）で雛形作成する。「コンポーネント作って」「ページ追加して」等でトリガ。
---

# create-component

## 目的

このリポジトリの React コード規約に従ったコンポーネント / ページの**雛形を作成**する。

規約は [AGENTS.md §3](../../../AGENTS.md) を参照。要点:

- ディレクトリ型 (`<Name>/index.tsx` + `<Name>/index.stories.tsx`)
- PascalCase
- `React.memo` でラップ
- `type Props` で型定義
- Storybook は必須

## 使い方

ユーザーが以下のようなリクエストをした時に実行:

- 「`Components/Button` を作って」
- 「管理画面に `UserDetail` ページを追加して」
- 「`Pages/Web/Profile` を作成」

## 手順

1. **置き場所を決定**する（ユーザー指示から判定）:
   - `Components/<Name>/` — 再利用可能 UI
   - `Pages/{Admin,Web}/<Name>/` — 画面
   - `Layouts/<Name>/` — レイアウト
   - `Pages/<section>/<Page>/components/<Name>/` — 特定ページ専用の子
2. **近い既存ファイルを1つ読む**（命名・import パス・import 文の書式を踏襲するため）。例:
   - `src/resources/js/Components/ModalView/index.tsx`
   - `src/resources/js/Pages/Web/Login/index.tsx`
3. **必要に応じて `hooks.ts` を作る**（state/副作用が複雑な場合）:
   - フォーム系のページは原則 `hooks.ts` を作り、`useXxx()` に閉じ込める
4. **下記のテンプレートをベース**に雛形を生成
5. `npm run lint` と `npm run type-check` で検証

## テンプレート

### Component: `index.tsx`

```tsx
import React from 'react'

type Props = {
    // TODO: props を定義
}

export const <Name> = React.memo<Props>(function <Name>({ }: Props) {
    return (
        <div>
            {/* TODO: 実装 */}
        </div>
    )
})
```

### Component: `index.stories.tsx`

```tsx
import { Meta, StoryObj } from '@storybook/react-vite'
import { <Name> } from '.'

const meta: Meta<typeof <Name>> = {
    title: '<category>/<Name>',
    component: <Name>,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    args: {
        // TODO: props
    },
}
```

`title` の `<category>` は配置場所に応じて:
- `components/<Name>`
- `pages/admin/<Name>` / `pages/web/<Name>`
- `layouts/<Name>`

### Page: `index.tsx`（Inertia ページ）

```tsx
import React from 'react'
import { Head } from '@inertiajs/react'
import { use<Name> } from './hooks'

type Props = {
    // TODO: Inertia から渡される props
}

export default function <Name>({ }: Props) {
    const { } = use<Name>()

    return (
        <>
            <Head title="<Name>" />
            {/* TODO: 実装 */}
        </>
    )
}
```

**注意**: ページは `export default` を使う（Inertia の規約）。再利用コンポーネントは named export。

### Page: `hooks.ts`

```ts
import { useForm } from '@inertiajs/react'

export const use<Name> = () => {
    const { data, setData, post, processing, errors } = useForm({
        // TODO: form fields
    })

    const submit: React.FormEventHandler = (e) => {
        e.preventDefault()
        // TODO: post(route('...'))
    }

    return { data, setData, processing, errors, submit }
}
```

## 禁止事項

- ❌ `Button.tsx` のようなフラットファイル形式では作らない（レガシー、新規は必ずディレクトリ型）
- ❌ `export default` を再利用コンポーネントに使わない（ページ以外は named export）
- ❌ Storybook ファイルを省略しない
- ❌ `any` 型を使わない
