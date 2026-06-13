import React from 'react'
import { Link, router } from '@inertiajs/react'
import Markdown from 'markdown-to-jsx'
import WebLayout from '@/Layouts/WebLayout'
import type { Post } from '@/types/yumeguri'

type Props = {
    post: Post
    canEdit: boolean
}

export const PostShowPage = React.memo(function PostShowPage({ post, canEdit }: Props) {
    const mapsUrl = post.lat != null && post.lng != null
        ? `https://www.google.com/maps?q=${post.lat},${post.lng}`
        : null

    const handleDelete = () => {
        if (!confirm('記事を削除しますか？')) return
        router.delete(`/posts/${post.id}`)
    }

    return (
        <WebLayout>
            <article className="px-4 py-6">
                <header className="mb-6 space-y-2">
                    <h1 className="text-2xl font-bold text-gray-900">{post.title}</h1>
                    <div className="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                        <span>📅 {post.visited_at}</span>
                        {post.place_name && <span>📍 {post.place_name}</span>}
                        {mapsUrl && (
                            <a
                                href={mapsUrl}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="text-amber-600 underline"
                            >
                                地図で見る
                            </a>
                        )}
                    </div>
                    <p className="text-xs text-gray-400">by {post.user.name}</p>
                </header>

                {post.body && (
                    <Markdown
                        options={{
                            overrides: {
                                h1: { props: { className: 'text-2xl font-bold mt-6 mb-2 text-gray-900' } },
                                h2: { props: { className: 'text-xl font-bold mt-5 mb-2 text-gray-800 border-b border-amber-100 pb-1' } },
                                h3: { props: { className: 'text-lg font-semibold mt-4 mb-1 text-gray-800' } },
                                p: { props: { className: 'mb-4 leading-relaxed text-gray-700' } },
                                ul: { props: { className: 'list-disc pl-5 mb-4 space-y-1 text-gray-700' } },
                                ol: { props: { className: 'list-decimal pl-5 mb-4 space-y-1 text-gray-700' } },
                                li: { props: { className: 'leading-relaxed' } },
                                img: { props: { className: 'w-full rounded-lg object-cover my-3' } },
                                a: { props: { className: 'text-amber-600 underline', target: '_blank', rel: 'noopener noreferrer' } },
                                strong: { props: { className: 'font-bold text-gray-900' } },
                                em: { props: { className: 'italic' } },
                                code: { props: { className: 'bg-amber-50 text-amber-900 px-1 py-0.5 rounded text-sm font-mono' } },
                                pre: { props: { className: 'bg-gray-900 text-green-300 p-4 rounded-lg overflow-x-auto my-3 text-sm font-mono' } },
                                blockquote: { props: { className: 'border-l-4 border-amber-300 pl-3 my-3 text-gray-600 italic' } },
                                hr: { props: { className: 'my-6 border-amber-100' } },
                            },
                        }}
                    >
                        {post.body}
                    </Markdown>
                )}

                {canEdit && (
                    <div className="mt-8 flex gap-3">
                        <Link
                            href={`/posts/${post.id}/edit`}
                            className="flex-1 rounded-full border border-amber-400 py-2 text-center text-sm font-semibold text-amber-700"
                        >
                            編集
                        </Link>
                        <button
                            type="button"
                            onClick={handleDelete}
                            className="rounded-full border border-red-300 px-5 py-2 text-sm text-red-600"
                        >
                            削除
                        </button>
                    </div>
                )}

                <div className="mt-6">
                    <Link href="/posts" className="text-sm text-amber-600 underline">
                        ← 記事一覧へ
                    </Link>
                </div>
            </article>
        </WebLayout>
    )
})

export default PostShowPage
