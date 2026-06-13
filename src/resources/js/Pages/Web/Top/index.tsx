import React from 'react'
import { Link } from '@inertiajs/react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import type { Post } from '@/types/yumeguri'

type UserSummary = {
    id: number
    name: string
    posts_count: number
}

type Props = {
    users: UserSummary[]
    latestPosts: Post[]
}

export const Top = React.memo(function Top({ users, latestPosts }: Props) {
    return (
        <WebLayout>
            <header className="bg-gradient-to-br from-amber-50 to-orange-100 py-12 text-center">
                <p className="text-xs uppercase tracking-[0.4em] text-amber-700">Personal Travel Blog</p>
                <h1 className="font-yuGothic text-4xl font-bold text-gray-900">YUMEGURI</h1>
                <p className="mt-2 text-sm text-gray-600">旅の記録を、Markdownで。</p>
            </header>

            <div className="px-4 py-8 space-y-10">
                {/* ユーザー一覧 */}
                <section>
                    <SectionTitle sub="MEMBERS">メンバー</SectionTitle>
                    <ul className="mt-4 flex flex-wrap gap-3">
                        {users.map((u) => (
                            <li key={u.id}>
                                <Link
                                    href={`/users/${u.id}`}
                                    className="flex items-center gap-2 rounded-full border border-amber-200 bg-white px-4 py-2 text-sm shadow-sm hover:bg-amber-50"
                                >
                                    <span className="font-semibold text-gray-900">{u.name}</span>
                                    {u.posts_count > 0 && (
                                        <span className="rounded-full bg-amber-100 px-2 py-0.5 text-xs text-amber-700">
                                            {u.posts_count}記事
                                        </span>
                                    )}
                                </Link>
                            </li>
                        ))}
                        {users.length === 0 && (
                            <li className="text-sm text-gray-400">まだメンバーがいません</li>
                        )}
                    </ul>
                </section>

                {/* 最新記事 */}
                <section>
                    <div className="flex items-center justify-between">
                        <SectionTitle sub="LATEST">最新の記事</SectionTitle>
                        <Link href="/posts" className="text-sm text-amber-600 underline">
                            すべて見る →
                        </Link>
                    </div>
                    <div className="mt-4 space-y-3">
                        {latestPosts.map((post) => (
                            <Link
                                key={post.id}
                                href={`/posts/${post.id}`}
                                className="block rounded-xl border border-amber-100 bg-white p-4 shadow-sm hover:bg-amber-50"
                            >
                                <h2 className="font-semibold text-gray-900">{post.title}</h2>
                                <div className="mt-1 flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                    <span className="font-medium text-amber-700">{post.user.name}</span>
                                    <span>📅 {post.visited_at}</span>
                                    {post.place_name && <span>📍 {post.place_name}</span>}
                                </div>
                                {post.body && (
                                    <p className="mt-2 line-clamp-2 text-sm text-gray-600">
                                        {post.body.replace(/!\[.*?\]\(.*?\)/g, '').replace(/[#*`>_[\]]/g, '').trim()}
                                    </p>
                                )}
                            </Link>
                        ))}
                        {latestPosts.length === 0 && (
                            <p className="text-sm text-gray-400">まだ記事がありません。</p>
                        )}
                    </div>
                </section>
            </div>
        </WebLayout>
    )
})

export default Top
