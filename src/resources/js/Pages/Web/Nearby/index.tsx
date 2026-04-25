import React, { useEffect, useState } from 'react'
import { router } from '@inertiajs/react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import SentoCard from '@/Components/SentoCard'
import type { SentoSummary } from '@/types/yumeguri'

type Props = {
    origin: { lat: number; lng: number }
    sentos: SentoSummary[]
}

type GeoStatus = 'idle' | 'requesting' | 'granted' | 'denied' | 'unsupported' | 'error'

const SAME_LOCATION_EPSILON = 0.0001
const GEO_FETCHED_KEY = 'yumeguri:nearby-geo-fetched'

export const Nearby = React.memo(function Nearby({ origin, sentos }: Props) {
    const [status, setStatus] = useState<GeoStatus>('idle')

    useEffect(() => {
        if (!('geolocation' in navigator)) {
            setStatus('unsupported')
            return
        }
        // 同一セッション内で一度取得済みなら再要求しない（リロード毎にダイアログを出さない）
        if (typeof sessionStorage !== 'undefined' && sessionStorage.getItem(GEO_FETCHED_KEY)) return

        setStatus('requesting')
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                sessionStorage.setItem(GEO_FETCHED_KEY, '1')
                setStatus('granted')
                const lat = pos.coords.latitude
                const lng = pos.coords.longitude
                const same =
                    Math.abs(lat - origin.lat) < SAME_LOCATION_EPSILON &&
                    Math.abs(lng - origin.lng) < SAME_LOCATION_EPSILON
                if (same) return
                router.get('/nearby', { lat, lng }, { preserveState: true, preserveScroll: true })
            },
            (err) => {
                setStatus(err.code === err.PERMISSION_DENIED ? 'denied' : 'error')
            },
            { timeout: 8000, maximumAge: 60_000 },
        )
    }, [origin.lat, origin.lng])

    return (
        <WebLayout>
            <div className="px-4 py-6">
                <SectionTitle sub="NEARBY">近くの未訪問銭湯</SectionTitle>
                <p className="mt-2 text-xs text-gray-500">
                    現在地: {origin.lat.toFixed(4)}, {origin.lng.toFixed(4)}
                </p>
                {status === 'denied' && (
                    <p className="mt-2 text-xs text-amber-700">
                        位置情報が許可されていません。デフォルト地点で表示しています。
                    </p>
                )}
                {status === 'unsupported' && (
                    <p className="mt-2 text-xs text-amber-700">
                        このブラウザは位置情報に対応していません。
                    </p>
                )}
                {status === 'error' && (
                    <p className="mt-2 text-xs text-amber-700">
                        位置情報の取得に失敗しました。デフォルト地点で表示しています。
                    </p>
                )}
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
