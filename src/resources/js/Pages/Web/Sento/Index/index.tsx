import React from 'react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import SentoCard from '@/Components/SentoCard'
import { useSentoIndex } from './hooks'
import type { Paginated, SentoSummary } from '@/types/yumeguri'

export type SentoIndexFilters = {
    prefecture?: string
    visited?: 'visited' | 'unvisited' | 'all'
    has_sauna?: boolean
    has_mizuburo?: boolean
    has_shampoo?: boolean
    has_soap?: boolean
    sort?: 'rating' | 'visited_at' | 'want_revisit'
}

type Props = {
    sentos: Paginated<SentoSummary>
    filters: SentoIndexFilters
}

const PREFECTURES = ['東京都', '神奈川県', '埼玉県', '千葉県']

export const SentoIndex = React.memo(function SentoIndex({ sentos, filters }: Props) {
    const { pending, set, apply } = useSentoIndex(filters)

    return (
        <WebLayout>
            <div className="px-4 py-6">
                <SectionTitle sub="SENTOS">銭湯一覧</SectionTitle>

                <form
                    onSubmit={(e) => {
                        e.preventDefault()
                        apply()
                    }}
                    className="mt-4 grid gap-3 rounded-lg border border-amber-100 bg-amber-50/40 p-4 md:grid-cols-3"
                >
                    <label className="flex flex-col gap-1 text-sm">
                        都道府県
                        <select
                            value={pending.prefecture ?? ''}
                            onChange={(e) => set('prefecture', e.target.value || undefined)}
                            className="rounded border-amber-200"
                        >
                            <option value="">すべて</option>
                            {PREFECTURES.map((p) => <option key={p} value={p}>{p}</option>)}
                        </select>
                    </label>
                    <label className="flex flex-col gap-1 text-sm">
                        訪問
                        <select
                            value={pending.visited ?? 'all'}
                            onChange={(e) => set('visited', e.target.value as SentoIndexFilters['visited'])}
                            className="rounded border-amber-200"
                        >
                            <option value="all">全て</option>
                            <option value="visited">訪問済み</option>
                            <option value="unvisited">未訪問</option>
                        </select>
                    </label>
                    <label className="flex flex-col gap-1 text-sm">
                        ソート
                        <select
                            value={pending.sort ?? 'rating'}
                            onChange={(e) => set('sort', e.target.value as SentoIndexFilters['sort'])}
                            className="rounded border-amber-200"
                        >
                            <option value="rating">★評価</option>
                            <option value="visited_at">訪問日</option>
                            <option value="want_revisit">再訪したい順</option>
                        </select>
                    </label>
                    <div className="flex flex-wrap gap-3 text-sm md:col-span-3">
                        {(['has_sauna', 'has_mizuburo', 'has_shampoo', 'has_soap'] as const).map((k) => (
                            <label key={k} className="inline-flex items-center gap-1.5">
                                <input
                                    type="checkbox"
                                    checked={!!pending[k]}
                                    onChange={(e) => set(k, e.target.checked || undefined)}
                                    className="rounded text-amber-600"
                                />
                                {{
                                    has_sauna: '🔥サウナ',
                                    has_mizuburo: '🧊水風呂',
                                    has_shampoo: '🧴シャンプー',
                                    has_soap: '🧼ボディソープ',
                                }[k]}
                            </label>
                        ))}
                        <button
                            type="submit"
                            className="ml-auto rounded-full bg-amber-700 px-5 py-1.5 text-sm font-semibold text-white"
                        >
                            適用
                        </button>
                    </div>
                </form>

                <div className="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {sentos.data.map((s) => <SentoCard key={s.id} sento={s} />)}
                </div>
                {sentos.data.length === 0 && (
                    <p className="mt-8 text-sm text-gray-500">条件に合致する銭湯がありません。</p>
                )}
            </div>
        </WebLayout>
    )
})

export default SentoIndex
