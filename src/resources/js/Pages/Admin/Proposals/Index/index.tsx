import React from 'react'
import { router } from '@inertiajs/react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import type { Paginated, SentoEditProposal } from '@/types/yumeguri'

type Props = {
    proposals: Paginated<SentoEditProposal>
}

export const ProposalsIndex = React.memo(function ProposalsIndex({ proposals }: Props) {
    const onAction = (id: number, action: 'approve' | 'reject') => {
        router.post(`/admin/proposals/${id}/${action}`, {}, { preserveScroll: true })
    }

    return (
        <WebLayout>
            <div className="px-4 py-6">
                <SectionTitle sub="ADMIN">編集提案</SectionTitle>
                <ul className="mt-4 space-y-3">
                    {proposals.data.map((p) => (
                        <li key={p.id} className="rounded border border-amber-100 bg-white p-4">
                            <div className="flex items-center justify-between">
                                <span className="font-semibold">
                                    {p.sento?.name ?? `銭湯 #${p.sento_id}`}
                                </span>
                                <Status status={p.status} />
                            </div>
                            <p className="mt-1 text-xs text-gray-500">
                                提案者: {p.proposer?.name} ・ {p.created_at}
                            </p>
                            {p.reason && <p className="mt-2 text-sm text-gray-700">理由: {p.reason}</p>}
                            <pre className="mt-2 overflow-x-auto rounded bg-amber-50 p-2 text-xs">
                                {JSON.stringify(p.changes, null, 2)}
                            </pre>
                            {p.status === 'pending' && (
                                <div className="mt-3 flex gap-2">
                                    <button
                                        onClick={() => onAction(p.id, 'approve')}
                                        className="rounded-full bg-amber-700 px-4 py-1 text-xs font-semibold text-white"
                                    >
                                        承認して反映
                                    </button>
                                    <button
                                        onClick={() => onAction(p.id, 'reject')}
                                        className="rounded-full border border-gray-300 px-4 py-1 text-xs"
                                    >
                                        却下
                                    </button>
                                </div>
                            )}
                        </li>
                    ))}
                </ul>
                {proposals.data.length === 0 && (
                    <p className="mt-6 text-sm text-gray-500">提案はありません。</p>
                )}
            </div>
        </WebLayout>
    )
})

const Status = ({ status }: { status: SentoEditProposal['status'] }) => {
    const css = status === 'approved'
        ? 'bg-emerald-100 text-emerald-800'
        : status === 'rejected'
            ? 'bg-gray-100 text-gray-700'
            : 'bg-amber-100 text-amber-800'
    const label = status === 'approved' ? '承認済' : status === 'rejected' ? '却下' : '保留中'
    return (
        <span className={`rounded-full px-2 py-0.5 text-xs ${css}`}>{label}</span>
    )
}

export default ProposalsIndex
