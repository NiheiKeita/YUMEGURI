import React from 'react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import type { SentoPhoto } from '@/types/yumeguri'

type Props = {
    profile: { id: number; name: string }
    photos: SentoPhoto[]
}

export const UserPhotos = React.memo(function UserPhotos({ profile, photos }: Props) {
    return (
        <WebLayout>
            <div className="px-4 py-6">
                <SectionTitle sub="PHOTOS">{profile.name} の写真</SectionTitle>
                <div className="mt-4 grid grid-cols-2 gap-2 md:grid-cols-3 lg:grid-cols-4">
                    {photos.map((p) => (
                        <figure key={p.id} className="overflow-hidden rounded">
                            <div
                                className="aspect-square w-full bg-gradient-to-br from-amber-100 to-orange-200"
                                style={p.url ? { backgroundImage: `url(${p.url})`, backgroundSize: 'cover' } : undefined}
                            />
                            {p.caption && (
                                <figcaption className="mt-1 text-xs text-gray-600">{p.caption}</figcaption>
                            )}
                        </figure>
                    ))}
                </div>
                {photos.length === 0 && (
                    <p className="mt-6 text-sm text-gray-500">まだ写真がありません。</p>
                )}
            </div>
        </WebLayout>
    )
})

export default UserPhotos
