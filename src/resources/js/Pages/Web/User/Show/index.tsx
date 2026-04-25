import React from 'react'
import { Link } from '@inertiajs/react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import RatingStars from '@/Components/RatingStars'
import type { SentoReview } from '@/types/yumeguri'

type Props = {
    profile: { id: number; name: string }
    reviews: SentoReview[]
    stats: { visited_count: number; prefecture_count: number; city_count: number }
}

export const UserShow = React.memo(function UserShow({ profile, reviews, stats }: Props) {
    return (
        <WebLayout>
            <div className="px-4 py-6">
                <header className="rounded-lg bg-gradient-to-br from-amber-50 to-orange-100 p-6">
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
