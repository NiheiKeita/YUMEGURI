import React from 'react'
import { Link } from '@inertiajs/react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import RatingStars from '@/Components/RatingStars'
import type { SentoReview } from '@/types/yumeguri'

type Props = {
    latestReviews: SentoReview[]
}

export const Top = React.memo(function Top({ latestReviews }: Props) {
    return (
        <WebLayout>
            <header className="bg-gradient-to-br from-amber-50 to-orange-100 py-12 text-center">
                <p className="text-xs uppercase tracking-[0.4em] text-amber-700">Tokyo · Kanagawa · Saitama · Chiba</p>
                <h1 className="font-yuGothic text-4xl font-bold text-gray-900">YUMEGURI</h1>
                <p className="mt-2 text-sm text-gray-600">湯気を辿る、街の小さな記録。</p>
            </header>

            <section className="px-4 py-8">
                <SectionTitle sub="LATEST">最新の訪問記録</SectionTitle>
                <div className="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {latestReviews.map((r) => (
                        <Link
                            key={r.id}
                            href={`/sentos/${r.sento_id}`}
                            className="block overflow-hidden rounded-lg border border-amber-100 bg-white shadow-sm hover:shadow-md"
                        >
                            <div className="aspect-video w-full bg-gradient-to-br from-amber-100 to-orange-200" />
                            <div className="p-4">
                                <p className="font-yuGothic text-base font-semibold text-gray-900">
                                    {r.sento?.name ?? '銭湯'}
                                </p>
                                <div className="mt-1 flex items-center gap-2 text-xs text-gray-500">
                                    <RatingStars value={r.rating} size="sm" />
                                    <span>{r.visited_at}</span>
                                </div>
                                {r.body && (
                                    <p className="mt-2 line-clamp-3 text-sm text-gray-700">{r.body}</p>
                                )}
                            </div>
                        </Link>
                    ))}
                    {latestReviews.length === 0 && (
                        <p className="text-sm text-gray-500">まだ訪問記録がありません。</p>
                    )}
                </div>
            </section>
        </WebLayout>
    )
})

export default Top
