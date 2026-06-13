import React from 'react'

type Props = {
    children: React.ReactNode
    sub?: string
    className?: string
}

// 雑誌の見出し風: 太い縦罫＋明朝体タイトル＋英字サブで誌面感を作る
export const SectionTitle = React.memo<Props>(function SectionTitle({
    children,
    sub,
    className = '',
}) {
    return (
        <div className={`flex items-end gap-3 border-l-4 border-amber-700 pl-3 ${className}`}>
            <h2 className="font-yuGothic text-xl font-semibold tracking-wide text-gray-900">
                {children}
            </h2>
            {sub && <span className="text-xs uppercase tracking-widest text-amber-700">{sub}</span>}
        </div>
    )
})

export default SectionTitle
