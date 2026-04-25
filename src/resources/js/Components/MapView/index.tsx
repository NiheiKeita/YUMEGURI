import React from 'react'
import type { Pin } from '@/types/yumeguri'

type Props = {
    pins: Pin[]
    center?: { lat: number; lng: number }
    height?: number
    className?: string
}

// Google Maps / Mapbox を統合する地点。
// 現状は雑誌風の placeholder で、ピン数と中心座標を可視化するだけ。
// 実装時には ref からマップライブラリを初期化し、pins を marker に展開する。
export const MapView = React.memo<Props>(function MapView({
    pins,
    center,
    height = 480,
    className = '',
}) {
    const visited = pins.filter((p) => p.visited).length
    const total = pins.length
    return (
        <div
            className={
                'relative w-full overflow-hidden rounded-lg border border-amber-100 ' +
                'bg-gradient-to-br from-sky-50 via-white to-amber-50 ' +
                className
            }
            style={{ height }}
        >
            <div className="absolute inset-0 grid place-items-center text-center text-gray-500">
                <div>
                    <p className="font-yuGothic text-lg">🗺 地図プレースホルダ</p>
                    <p className="mt-2 text-sm">ピン: {total}件（うち訪問済み: {visited}件）</p>
                    {center && (
                        <p className="mt-1 text-xs">
                            中心: {center.lat.toFixed(4)}, {center.lng.toFixed(4)}
                        </p>
                    )}
                    <p className="mt-3 text-xs text-amber-700">
                        TODO: Google Maps / Mapbox を差し込む
                    </p>
                </div>
            </div>
        </div>
    )
})

export default MapView
