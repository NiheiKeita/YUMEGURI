import React from 'react'

type Props = {
    value: number
    max?: number
    size?: 'sm' | 'md' | 'lg'
    className?: string
}

export const RatingStars = React.memo<Props>(function RatingStars({
    value,
    max = 5,
    size = 'md',
    className = '',
}) {
    const sizeCss = size === 'sm' ? 'text-sm' : size === 'lg' ? 'text-2xl' : 'text-base'
    const filled = Math.round(value)
    return (
        <span aria-label={`${value} of ${max} stars`} className={`${sizeCss} ${className}`}>
            {Array.from({ length: max }).map((_, i) => (
                <span key={i} className={i < filled ? 'text-amber-500' : 'text-gray-300'}>
                    ★
                </span>
            ))}
        </span>
    )
})

export default RatingStars
