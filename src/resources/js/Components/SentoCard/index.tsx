import { Link } from '@inertiajs/react'
import React from 'react'
import RatingStars from '@/Components/RatingStars'
import EquipmentChip from '@/Components/EquipmentChip'
import type { SentoSummary } from '@/types/yumeguri'

type Props = {
    sento: SentoSummary
    href?: string
    className?: string
}

export const SentoCard = React.memo<Props>(function SentoCard({ sento, href, className = '' }) {
    const link = href ?? `/sentos/${sento.id}`
    return (
        <Link
            href={link}
            className={
                'block overflow-hidden rounded-lg border border-amber-100 bg-white shadow-sm transition hover:shadow-md ' +
                className
            }
        >
            <div className="aspect-video w-full bg-gradient-to-br from-amber-100 to-orange-200" />
            <div className="p-4">
                <div className="flex items-baseline justify-between gap-2">
                    <h3 className="font-yuGothic text-lg font-semibold text-gray-900">
                        {sento.name}
                    </h3>
                    {sento.avg_rating != null && (
                        <RatingStars value={sento.avg_rating} size="sm" />
                    )}
                </div>
                <p className="mt-1 text-xs text-gray-500">
                    {sento.prefecture}
                    {sento.city ? ` ・ ${sento.city}` : ''}
                </p>
                {sento.nearest_station && (
                    <p className="mt-1 text-xs text-gray-600">
                        🚉 {sento.nearest_station}
                        {sento.walk_minutes != null ? ` 徒歩${sento.walk_minutes}分` : ''}
                    </p>
                )}
                <div className="mt-3 flex flex-wrap gap-1.5">
                    {sento.has_shampoo && <EquipmentChip variant="shampoo" />}
                    {sento.has_soap && <EquipmentChip variant="soap" />}
                </div>
                {sento.distance_km != null && (
                    <p className="mt-3 text-xs text-amber-700">
                        現在地から {sento.distance_km.toFixed(1)} km
                    </p>
                )}
            </div>
        </Link>
    )
})

export default SentoCard
