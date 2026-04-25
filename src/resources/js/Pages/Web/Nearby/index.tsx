import React, { useEffect } from 'react'
import { router } from '@inertiajs/react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import SentoCard from '@/Components/SentoCard'
import type { SentoSummary } from '@/types/yumeguri'

type Props = {
    origin: { lat: number; lng: number }
    sentos: SentoSummary[]
}

export const Nearby = React.memo(function Nearby({ origin, sentos }: Props) {
    useEffect(() => {
        if (!('geolocation' in navigator)) return
        navigator.geolocation.getCurrentPosition((pos) => {
            const lat = pos.coords.latitude
            const lng = pos.coords.longitude
            // 既に同じ場所なら再リクエストしない
            if (Math.abs(lat - origin.lat) < 0.0001 && Math.abs(lng - origin.lng) < 0.0001) return
            router.get('/nearby', { lat, lng }, { preserveState: true, preserveScroll: true })
        })
    }, [origin.lat, origin.lng])

    return (
        <WebLayout>
            <div className="px-4 py-6">
                <SectionTitle sub="NEARBY">近くの未訪問銭湯</SectionTitle>
                <p className="mt-2 text-xs text-gray-500">
                    現在地: {origin.lat.toFixed(4)}, {origin.lng.toFixed(4)}
                </p>
                <div className="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {sentos.map((s) => <SentoCard key={s.id} sento={s} />)}
                </div>
                {sentos.length === 0 && (
                    <p className="mt-6 text-sm text-gray-500">近くに未訪問の銭湯はありません。</p>
                )}
            </div>
        </WebLayout>
    )
})

export default Nearby
