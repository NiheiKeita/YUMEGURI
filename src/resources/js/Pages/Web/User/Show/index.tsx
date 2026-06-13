import React from 'react'
import { Link } from '@inertiajs/react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import RatingStars from '@/Components/RatingStars'
import type { Post, SentoReview } from '@/types/yumeguri'

type Props = {
    profile: { id: number; name: string }
    reviews: SentoReview[]
    posts: Post[]
    stats: { visited_count: number; prefecture_count: number; city_count: number }
    canEditProfile?: boolean
}

export const UserShow = React.memo(function UserShow({ profile, reviews, posts, stats, canEditProfile = false }: Props) {
    const [isMenuOpen, setIsMenuOpen] = React.useState(false)

    return (
        <WebLayout>
            <div className="px-4 py-6">
                <header className="relative rounded-lg bg-gradient-to-br from-amber-50 to-orange-100 p-6">
                    {canEditProfile && (
                        <div className="absolute right-4 top-4">
                            <button
                                type="button"
                                aria-label="プロフィールメニュー"
                                aria-expanded={isMenuOpen}
                                onClick={() => setIsMenuOpen((current) => !current)}
                                className="flex h-10 w-10 items-center justify-center rounded-full border border-amber-200 bg-white text-xl font-bold text-amber-900 shadow-sm"
                            >
                                ≡
                            </button>
                            {isMenuOpen && (
                                <div className="absolute right-0 top-12 w-48 rounded-lg border border-amber-100 bg-white p-2 shadow-lg">
                                    <Link
                                        href="/posts/create"
                                        className="block rounded px-3 py-2 text-sm font-semibold text-gray-800 hover:bg-amber-50"
                                    >
                                        ✏️ 記事を書く
                                    </Link>
                                    <hr className="my-1 border-amber-100" />
                                    <Link
                                        href={route('web.users.edit', profile.id)}
                                        className="block rounded px-3 py-2 text-sm text-gray-600 hover:bg-amber-50"
                                    >
                                        プロフィール編集
                                    </Link>
                                </div>
                            )}
                        </div>
                    )}
                    <p className="text-xs uppercase tracking-[0.4em] text-amber-700">User</p>
                    <h1 className="font-yuGothic text-2xl font-bold">{profile.name}</h1>
                    <dl className="mt-4 grid grid-cols-3 gap-2 text-center text-sm">
                        <Stat label="訪問" value={stats.visited_count} />
                        <Stat label="都道府県" value={stats.prefecture_count} />
                        <Stat label="区市町村" value={stats.city_count} />
                    </dl>
                    <div className="mt-4 flex flex-wrap gap-2 text-sm">
                        <Link href={`/users/${profile.id}/map`} className="rounded-full border border-amber-700 px-4 py-1 text-amber-800">
                            🗺 マップ
                        </Link>
                        <Link href={`/users/${profile.id}/photos`} className="rounded-full border border-amber-700 px-4 py-1 text-amber-800">
                            📸 写真
                        </Link>
                        <Link href={`/users/${profile.id}/nearby`} className="rounded-full border border-amber-700 px-4 py-1 text-amber-800">
                            📍 近くの未訪問
                        </Link>
                    </div>
                </header>

                <SectionTitle sub="VISITED" className="mt-8">訪問済みの銭湯</SectionTitle>
                <ul className="mt-4 space-y-3">
                    {reviews.map((r) => (
                        <li key={r.id} className="rounded border border-amber-100 bg-white p-3">
                            <div className="flex items-center justify-between">
                                <Link href={`/sentos/${r.sento_id}`} className="font-semibold text-gray-900">
                                    {r.sento?.name}
                                </Link>
                                <RatingStars value={r.rating} size="sm" />
                            </div>
                            <p className="text-xs text-gray-500">{r.visited_at}</p>
                            {r.want_revisit && <span className="text-xs text-amber-700">♨ また行きたい</span>}
                        </li>
                    ))}
                    {reviews.length === 0 && (
                        <li className="text-sm text-gray-500">まだ訪問記録がありません。</li>
                    )}
                </ul>

                {/* ブログ記事 */}
                <SectionTitle sub="POSTS" className="mt-8">ブログ記事</SectionTitle>
                <div className="mt-4 space-y-3">
                    {posts.map((post) => (
                        <Link
                            key={post.id}
                            href={`/posts/${post.id}`}
                            className="block rounded-xl border border-amber-100 bg-white p-4 shadow-sm hover:bg-amber-50"
                        >
                            <h3 className="font-semibold text-gray-900">{post.title}</h3>
                            <div className="mt-1 flex flex-wrap gap-2 text-xs text-gray-500">
                                <span>📅 {post.visited_at}</span>
                                {post.place_name && <span>📍 {post.place_name}</span>}
                            </div>
                            {post.body && (
                                <p className="mt-1 line-clamp-2 text-sm text-gray-600">
                                    {post.body.replace(/!\[.*?\]\(.*?\)/g, '').replace(/[#*`>_[\]]/g, '').trim()}
                                </p>
                            )}
                        </Link>
                    ))}
                    {posts.length === 0 && (
                        <p className="text-sm text-gray-500">まだ記事がありません。</p>
                    )}
                </div>
            </div>
        </WebLayout>
    )
})

const Stat = ({ label, value }: { label: string; value: number }) => (
    <div>
        <dt className="text-xs text-amber-700">{label}</dt>
        <dd className="font-yuGothic text-2xl font-bold">{value}</dd>
    </div>
)

export default UserShow
