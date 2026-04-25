import React from 'react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import SentoCard from '@/Components/SentoCard'
import type { SentoSummary } from '@/types/yumeguri'

type Props = {
    profile: { id: number; name: string }
    origin: { lat: number; lng: number }
    sentos: SentoSummary[]
}

// 自分のページのみ表示される（友達のページではバックエンドが 404 にしている）
export const UserNearby = React.memo(function UserNearby({ profile, origin, sentos }: Props) {
    return (
        <WebLayout>
            <div className="px-4 py-6">
                <SectionTitle sub="NEARBY">近くの未訪問銭湯</SectionTitle>
                <p className="mt-2 text-xs text-gray-500">
                    {profile.name} の現在地: {origin.lat.toFixed(4)}, {origin.lng.toFixed(4)}
                </p>
                <div className="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {sentos.map((s) => <SentoCard key={s.id} sento={s} />)}
                </div>
            </div>
        </WebLayout>
    )
})

export default UserNearby
