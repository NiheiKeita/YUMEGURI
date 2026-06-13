import React from 'react'
import { Link } from '@inertiajs/react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import type { Paginated, Post } from '@/types/yumeguri'

type Props = {
    posts: Paginated<Post>
}

export const PostIndexPage = React.memo(function PostIndexPage({ posts }: Props) {
    return (
        <WebLayout>
            <div className="px-4 py-6">
                <div className="mb-4 flex items-center justify-between">
                    <SectionTitle sub="BLOG">記事一覧</SectionTitle>
                    <Link
                        href="/posts/create"
                        className="rounded-full bg-amber-600 px-4 py-2 text-sm font-semibold text-white"
                    >
                        + 記事を書く
                    </Link>
                </div>

                {posts.data.length === 0 ? (
                    <p className="mt-10 text-center text-gray-400">まだ記事がありません</p>
                ) : (
                    <div className="space-y-4">
                        {posts.data.map((post) => (
                            <PostCard key={post.id} post={post} />
                        ))}
                    </div>
                )}

                {(posts.meta.last_page > 1) && (
                    <div className="mt-6 flex justify-center gap-2">
                        {posts.links.prev && (
                            <Link
                                href={posts.links.prev}
                                className="rounded border border-amber-300 px-3 py-1 text-sm text-amber-700"
                            >
                                ← 前
                            </Link>
                        )}
                        <span className="px-3 py-1 text-sm text-gray-500">
                            {posts.meta.current_page} / {posts.meta.last_page}
                        </span>
                        {posts.links.next && (
                            <Link
                                href={posts.links.next}
                                className="rounded border border-amber-300 px-3 py-1 text-sm text-amber-700"
                            >
                                次 →
                            </Link>
                        )}
                    </div>
                )}
            </div>
        </WebLayout>
    )
})

const PostCard = React.memo(function PostCard({ post }: { post: Post }) {
    const excerpt = post.body
        ?.replace(/!\[.*?\]\(.*?\)/g, '')
        .replace(/[#*`>_[\]]/g, '')
        .trim()
        .slice(0, 80)

    return (
        <Link href={`/posts/${post.id}`} className="block">
            <div className="overflow-hidden rounded-xl border border-amber-100 bg-white p-4 shadow-sm">
                <h2 className="text-base font-bold text-gray-900">{post.title}</h2>
                <div className="mt-1 flex flex-wrap gap-2 text-xs text-gray-500">
                    <span>📅 {post.visited_at}</span>
                    {post.place_name && <span>📍 {post.place_name}</span>}
                </div>
                {excerpt && (
                    <p className="mt-2 line-clamp-2 text-sm text-gray-600">{excerpt}…</p>
                )}
            </div>
        </Link>
    )
})

export default PostIndexPage
