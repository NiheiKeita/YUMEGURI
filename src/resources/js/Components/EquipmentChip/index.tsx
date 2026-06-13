import React from 'react'

type Variant = 'sauna' | 'mizuburo' | 'tansan' | 'kusuri' | 'silk' | 'denki' | 'shampoo' | 'soap'

type Props = {
    variant: Variant
    label?: string
    className?: string
}

const ICONS: Record<Variant, string> = {
    sauna: '🔥',
    mizuburo: '🧊',
    tansan: '💧',
    kusuri: '🌿',
    silk: '🥛',
    denki: '⚡',
    shampoo: '🧴',
    soap: '🧼',
}

const DEFAULT_LABEL: Record<Variant, string> = {
    sauna: 'サウナ',
    mizuburo: '水風呂',
    tansan: '炭酸泉',
    kusuri: '薬湯',
    silk: 'シルク',
    denki: '電気風呂',
    shampoo: 'シャンプー',
    soap: 'ボディソープ',
}

// 雑誌風: 暖色系の枠線で抜き感を演出。設備の有無を視覚的に伝える小さなチップ。
export const EquipmentChip = React.memo<Props>(function EquipmentChip({
    variant,
    label,
    className = '',
}) {
    return (
        <span
            className={
                'inline-flex items-center gap-1 rounded-full border border-amber-300 bg-amber-50 ' +
                'px-3 py-1 text-xs text-amber-900 ' +
                className
            }
        >
            <span aria-hidden>{ICONS[variant]}</span>
            <span>{label ?? DEFAULT_LABEL[variant]}</span>
        </span>
    )
})

export default EquipmentChip
