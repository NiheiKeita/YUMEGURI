import React from 'react'
import { Link } from '@inertiajs/react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import RatingStars from '@/Components/RatingStars'
import EquipmentChip from '@/Components/EquipmentChip'
import type { SentoDetail } from '@/types/yumeguri'

type Props = { sento: SentoDetail }

export const SentoShow = React.memo(function SentoShow({ sento }: Props) {
    const reviews = sento.reviews ?? []
    return (
        <WebLayout>
            <article className="px-4 py-6">
                <p className="text-xs uppercase tracking-[0.3em] text-amber-700">
                    {sento.prefecture}
                    {sento.city ? ` · ${sento.city}` : ''}
                </p>
                <h1 className="mt-1 font-yuGothic text-3xl font-bold text-gray-900">{sento.name}</h1>
                {sento.name_kana && <p className="mt-1 text-sm text-gray-500">{sento.name_kana}</p>}

                <div className="mt-4 aspect-video w-full rounded-lg bg-gradient-to-br from-amber-100 to-orange-200" />

                <dl className="mt-6 grid gap-3 text-sm md:grid-cols-2">
                    <Row label="住所" value={sento.address} />
                    <Row label="電話" value={sento.phone ?? '—'} />
                    <Row label="営業時間" value={sento.hours ?? '—'} />
                    <Row label="定休日" value={sento.closed_days ?? '—'} />
                    <Row label="料金" value={sento.price != null ? `¥${sento.price}` : '—'} />
                    <Row
                        label="アクセス"
                        value={sento.nearest_station
                            ? `${sento.nearest_station}${sento.walk_minutes != null ? ` 徒歩${sento.walk_minutes}分` : ''}`
                            : '—'}
                    />
                </dl>

                <div className="mt-4 flex flex-wrap gap-1.5">
                    {sento.has_shampoo && <EquipmentChip variant="shampoo" />}
                    {sento.has_soap && <EquipmentChip variant="soap" />}
                </div>

                <div className="mt-6 flex flex-wrap gap-3">
                    <Link
                        href={`/sentos/${sento.id}/review`}
                        className="rounded-full bg-amber-700 px-5 py-2 text-sm font-semibold text-white"
                    >
                        記録する / 編集
                    </Link>
                    <Link
                        href={`/sentos/${sento.id}/propose`}
                        className="rounded-full border border-amber-600 px-5 py-2 text-sm font-semibold text-amber-700"
                    >
                        情報を直す
                    </Link>
                </div>

                <section className="mt-10">
                    <SectionTitle sub="REVIEWS">みんなの感想</SectionTitle>
                    <ul className="mt-4 space-y-4">
                        {reviews.map((r) => (
                            <li key={r.id} className="rounded-lg border border-amber-100 bg-white p-4">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm font-semibold">{r.user?.name}</span>
                                    <RatingStars value={r.rating} size="sm" />
                                </div>
                                <p className="text-xs text-gray-500">{r.visited_at}</p>
                                {r.body && <p className="mt-2 text-sm text-gray-800">{r.body}</p>}
                                <div className="mt-2 flex flex-wrap gap-1.5">
                                    {r.has_sauna && <EquipmentChip variant="sauna" label={`サウナ${r.sauna_temp ?? ''}℃`} />}
                                    {r.has_mizuburo && <EquipmentChip variant="mizuburo" label={`水風呂${r.mizuburo_temp ?? ''}℃`} />}
                                </div>
                            </li>
                        ))}
                        {reviews.length === 0 && (
                            <li className="text-sm text-gray-500">まだ感想はありません。</li>
                        )}
                    </ul>
                </section>
            </article>
        </WebLayout>
    )
})

const Row = ({ label, value }: { label: string; value: string }) => (
    <div className="flex gap-3 border-b border-amber-50 py-2">
        <dt className="w-24 shrink-0 text-xs font-semibold text-amber-700">{label}</dt>
        <dd className="text-sm text-gray-800">{value}</dd>
    </div>
)

export default SentoShow
