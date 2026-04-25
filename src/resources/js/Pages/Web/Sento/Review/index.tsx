import React from 'react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import { useReviewForm } from './hooks'
import type { SentoDetail, SentoReview } from '@/types/yumeguri'

type Props = {
    sento: SentoDetail
    review: SentoReview | null
}

const BATH_TYPES = ['炭酸泉', '薬湯', 'シルク', '電気風呂', '露天', 'ジェット']

export const SentoReviewPage = React.memo(function SentoReviewPage({ sento, review }: Props) {
    const { data, set, submit, processing, errors } = useReviewForm(sento.id, review)

    return (
        <WebLayout>
            <form onSubmit={submit} className="space-y-6 px-4 py-6">
                <SectionTitle sub="REVIEW">{sento.name} の記録</SectionTitle>

                <Field label="訪問日" error={errors.visited_at}>
                    <input
                        type="date"
                        value={data.visited_at}
                        onChange={(e) => set('visited_at', e.target.value)}
                        className="w-full rounded border-amber-200"
                    />
                </Field>

                <Field label="★評価" error={errors.rating}>
                    <input
                        type="range" min={1} max={5}
                        value={data.rating}
                        onChange={(e) => set('rating', Number(e.target.value))}
                        className="w-full"
                    />
                    <span className="text-sm">{data.rating} / 5</span>
                </Field>

                <Field label="感想" error={errors.body}>
                    <textarea
                        value={data.body}
                        onChange={(e) => set('body', e.target.value)}
                        rows={4}
                        className="w-full rounded border-amber-200"
                    />
                </Field>

                <fieldset className="grid gap-3 md:grid-cols-2">
                    <Toggle label="🔥 サウナあり" checked={data.has_sauna} onChange={(v) => set('has_sauna', v)} />
                    <Field label="サウナ温度(℃)">
                        <input
                            type="number"
                            value={data.sauna_temp ?? ''}
                            onChange={(e) => set('sauna_temp', e.target.value === '' ? null : Number(e.target.value))}
                            className="w-full rounded border-amber-200"
                            disabled={!data.has_sauna}
                        />
                    </Field>
                    <Toggle label="🧊 水風呂あり" checked={data.has_mizuburo} onChange={(v) => set('has_mizuburo', v)} />
                    <Field label="水風呂温度(℃)">
                        <input
                            type="number"
                            value={data.mizuburo_temp ?? ''}
                            onChange={(e) => set('mizuburo_temp', e.target.value === '' ? null : Number(e.target.value))}
                            className="w-full rounded border-amber-200"
                            disabled={!data.has_mizuburo}
                        />
                    </Field>
                </fieldset>

                <Field label="お湯の種類">
                    <div className="flex flex-wrap gap-2">
                        {BATH_TYPES.map((b) => {
                            const active = data.bath_types.includes(b)
                            return (
                                <button
                                    key={b} type="button"
                                    onClick={() => set('bath_types',
                                        active ? data.bath_types.filter((x) => x !== b) : [...data.bath_types, b],
                                    )}
                                    className={
                                        'rounded-full border px-3 py-1 text-xs ' +
                                        (active
                                            ? 'border-amber-700 bg-amber-700 text-white'
                                            : 'border-amber-300 bg-white text-amber-800')
                                    }
                                >
                                    {b}
                                </button>
                            )
                        })}
                    </div>
                </Field>

                <Field label="混雑度 (1-5)">
                    <input
                        type="number" min={1} max={5}
                        value={data.crowding ?? ''}
                        onChange={(e) => set('crowding', e.target.value === '' ? null : Number(e.target.value))}
                        className="w-full rounded border-amber-200"
                    />
                </Field>

                <Field label="おすすめ時間帯">
                    <input
                        value={data.best_time ?? ''}
                        onChange={(e) => set('best_time', e.target.value)}
                        className="w-full rounded border-amber-200"
                    />
                </Field>

                <Toggle
                    label="また行きたい"
                    checked={data.want_revisit}
                    onChange={(v) => set('want_revisit', v)}
                />

                <button
                    type="submit" disabled={processing}
                    className="w-full rounded-full bg-amber-700 py-3 font-semibold text-white disabled:opacity-50"
                >
                    保存する
                </button>
            </form>
        </WebLayout>
    )
})

const Field = ({
    label, children, error,
}: { label: string; children: React.ReactNode; error?: string }) => (
    <label className="block">
        <span className="text-xs font-semibold text-amber-700">{label}</span>
        <div className="mt-1">{children}</div>
        {error && <p className="mt-1 text-xs text-red-600">{error}</p>}
    </label>
)

const Toggle = ({
    label, checked, onChange,
}: { label: string; checked: boolean; onChange: (v: boolean) => void }) => (
    <label className="inline-flex items-center gap-2 text-sm">
        <input
            type="checkbox"
            checked={checked}
            onChange={(e) => onChange(e.target.checked)}
            className="rounded text-amber-600"
        />
        {label}
    </label>
)

export default SentoReviewPage
