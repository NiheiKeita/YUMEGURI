import React from 'react'
import { useForm } from '@inertiajs/react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import type { SentoDetail } from '@/types/yumeguri'

type Props = { sento: SentoDetail }

// admin 専用: 直接編集。承認フローを経ずに上書きするので最小限の項目のみ。
export const SentoEdit = React.memo(function SentoEdit({ sento }: Props) {
    const { data, setData, patch, processing, errors } = useForm({
        name: sento.name,
        address: sento.address,
        hours: sento.hours ?? '',
        closed_days: sento.closed_days ?? '',
        price: sento.price ?? null,
        status: sento.status,
        has_shampoo: sento.has_shampoo,
        has_soap: sento.has_soap,
    })

    return (
        <WebLayout>
            <form
                onSubmit={(e) => {
                    e.preventDefault()
                    patch(`/sentos/${sento.id}`)
                }}
                className="space-y-4 px-4 py-6"
            >
                <SectionTitle sub="ADMIN">{sento.name} を直接編集</SectionTitle>

                <Row label="銭湯名" error={errors.name}>
                    <input
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        className="w-full rounded border-amber-200"
                    />
                </Row>
                <Row label="住所" error={errors.address}>
                    <input
                        value={data.address}
                        onChange={(e) => setData('address', e.target.value)}
                        className="w-full rounded border-amber-200"
                    />
                </Row>
                <Row label="営業時間">
                    <input
                        value={data.hours}
                        onChange={(e) => setData('hours', e.target.value)}
                        className="w-full rounded border-amber-200"
                    />
                </Row>
                <Row label="定休日">
                    <input
                        value={data.closed_days}
                        onChange={(e) => setData('closed_days', e.target.value)}
                        className="w-full rounded border-amber-200"
                    />
                </Row>
                <Row label="料金">
                    <input
                        type="number"
                        value={data.price ?? ''}
                        onChange={(e) => setData('price', e.target.value === '' ? null : Number(e.target.value))}
                        className="w-full rounded border-amber-200"
                    />
                </Row>
                <Row label="営業状態">
                    <select
                        value={data.status}
                        onChange={(e) => setData('status', e.target.value as typeof data.status)}
                        className="w-full rounded border-amber-200"
                    >
                        <option value="open">営業中</option>
                        <option value="closed_temp">休業中</option>
                        <option value="closed_perm">廃業</option>
                    </select>
                </Row>
                <div className="flex gap-4">
                    <label className="inline-flex items-center gap-2">
                        <input
                            type="checkbox" checked={data.has_shampoo}
                            onChange={(e) => setData('has_shampoo', e.target.checked)}
                        /> 🧴 シャンプー
                    </label>
                    <label className="inline-flex items-center gap-2">
                        <input
                            type="checkbox" checked={data.has_soap}
                            onChange={(e) => setData('has_soap', e.target.checked)}
                        /> 🧼 ボディソープ
                    </label>
                </div>

                <button
                    type="submit"
                    disabled={processing}
                    className="w-full rounded-full bg-amber-700 py-3 font-semibold text-white disabled:opacity-50"
                >
                    更新する
                </button>
            </form>
        </WebLayout>
    )
})

const Row = ({
    label, children, error,
}: { label: string; children: React.ReactNode; error?: string }) => (
    <label className="block">
        <span className="text-xs font-semibold text-amber-700">{label}</span>
        <div className="mt-1">{children}</div>
        {error && <p className="text-xs text-red-600">{error}</p>}
    </label>
)

export default SentoEdit
