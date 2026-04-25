import React from 'react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import MapView from '@/Components/MapView'
import type { Pin } from '@/types/yumeguri'

type Props = { pins: Pin[] }

export const Map = React.memo(function Map({ pins }: Props) {
    return (
        <WebLayout>
            <div className="px-4 py-6">
                <SectionTitle sub="MAP">銭湯マップ</SectionTitle>
                <p className="mt-2 text-sm text-gray-600">
                    塗りつぶし: 訪問済み / 枠線のみ: 未訪問
                </p>
                <div className="mt-4">
                    <MapView pins={pins} height={520} />
                </div>
            </div>
        </WebLayout>
    )
})

export default Map
