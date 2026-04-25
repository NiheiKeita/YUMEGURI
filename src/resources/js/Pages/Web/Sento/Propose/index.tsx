import React from 'react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import { useProposeForm } from './hooks'
import type { SentoDetail } from '@/types/yumeguri'

type Props = { sento: SentoDetail }

export const SentoPropose = React.memo(function SentoPropose({ sento }: Props) {
    const { data, setChange, setReason, submit, processing, errors } = useProposeForm(sento)

    return (
        <WebLayout>
            <form onSubmit={submit} className="space-y-4 px-4 py-6">
                <SectionTitle sub="PROPOSE">{sento.name} の情報を直す</SectionTitle>
                <p className="text-sm text-gray-600">
                    変更したい項目だけ書き換えてください。承認されると本データに反映されます。
                </p>

                {(['name', 'address', 'phone', 'hours', 'closed_days', 'price', 'nearest_station', 'walk_minutes'] as const).map((key) => {
                    // useForm の errors はネストしたキーも来るが型が string キーに閉じている
                    const errorMap = errors as unknown as Record<string, string | undefined>
                    const errMessage = errorMap[`changes.${key}`]
                    return (
                        <label key={key} className="block">
                            <span className="text-xs font-semibold text-amber-700">{LABEL[key]}</span>
                            <input
                                type={key === 'price' || key === 'walk_minutes' ? 'number' : 'text'}
                                value={data.changes[key] ?? ''}
                                placeholder={String((sento as Record<string, unknown>)[key] ?? '')}
                                onChange={(e) => setChange(key, e.target.value === '' ? '' : e.target.value)}
                                className="mt-1 w-full rounded border-amber-200"
                            />
                            {errMessage && (
                                <p className="text-xs text-red-600">{errMessage}</p>
                            )}
                        </label>
                    )
                })}

                <label className="block">
                    <span className="text-xs font-semibold text-amber-700">変更の根拠・理由</span>
                    <textarea
                        rows={3}
                        value={data.reason}
                        onChange={(e) => setReason(e.target.value)}
                        className="mt-1 w-full rounded border-amber-200"
                    />
                </label>

                <button
                    type="submit"
                    disabled={processing}
                    className="w-full rounded-full bg-amber-700 py-3 font-semibold text-white disabled:opacity-50"
                >
                    提案を送信
                </button>
            </form>
        </WebLayout>
    )
})

const LABEL: Record<string, string> = {
    name: '銭湯名',
    address: '住所',
    phone: '電話',
    hours: '営業時間',
    closed_days: '定休日',
    price: '料金(円)',
    nearest_station: '最寄り駅',
    walk_minutes: '徒歩分数',
}

export default SentoPropose
