
import React from 'react'

type Props = {
    children: React.ReactNode
}
export const WebLayout = React.memo<Props>(function WebLayout({
    children,
}) {
    return (
        <div className="flex min-h-screen flex-col items-center bg-white font-yuGothic">
            <div className="w-full max-w-6xl overflow-hidden p-1">
                {children}
            </div>
        </div>
    )

})
export default WebLayout
