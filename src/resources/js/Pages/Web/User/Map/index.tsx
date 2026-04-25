import React from 'react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import MapView from '@/Components/MapView'
import type { Pin } from '@/types/yumeguri'

type Props = {
    profile: { id: number; name: string }
    pins: Pin[]
}

export const UserMap = React.memo(function UserMap({ profile, pins }: Props) {
    return (
        <WebLayout>
            <div className="px-4 py-6">
                <SectionTitle sub="USER MAP">{profile.name} の足跡</SectionTitle>
                <p className="mt-2 text-sm text-gray-600">訪問済みの銭湯だけをピンで表示します。</p>
                <div className="mt-4">
                    <MapView pins={pins.map((p) => ({ ...p, visited: true }))} height={520} />
                </div>
            </div>
        </WebLayout>
    )
})

export default UserMap
