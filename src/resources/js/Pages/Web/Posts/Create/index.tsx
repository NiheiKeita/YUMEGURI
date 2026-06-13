import React, { useRef } from 'react'
import WebLayout from '@/Layouts/WebLayout'
import SectionTitle from '@/Components/SectionTitle'
import { usePostForm } from './hooks'
import type { Post } from '@/types/yumeguri'

type Props = {
    post?: Post | null
}

export const PostCreatePage = React.memo(function PostCreatePage({ post = null }: Props) {
    const {
        form, set, errors, processing, gpsLoading, imageUploading,
        textareaRef, fetchGps, uploadImage, submit,
    } = usePostForm(post)

    const fileInputRef = useRef<HTMLInputElement>(null)
    const isEdit = post != null

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0]
        if (file) void uploadImage(file)
        e.target.value = ''
    }

    return (
        <WebLayout>
            <form className="space-y-5 px-4 py-6">
                <SectionTitle sub={isEdit ? 'EDIT' : 'NEW'}>
                    {isEdit ? '記事を編集' : '記事を書く'}
                </SectionTitle>

                <Field label="タイトル *" error={errors.title}>
                    <input
                        type="text"
                        value={form.title}
                        onChange={(e) => set('title', e.target.value)}
                        placeholder="今日はどこへ行きましたか？"
                        className="w-full rounded-lg border border-amber-200 px-3 py-2 text-base focus:border-amber-400 focus:outline-none"
                    />
                </Field>

                <Field label="場所名（任意）" error={errors.place_name}>
                    <input
                        type="text"
                        value={form.place_name}
                        onChange={(e) => set('place_name', e.target.value)}
                        placeholder="浅草、上野公園 など"
                        className="w-full rounded-lg border border-amber-200 px-3 py-2 text-base focus:border-amber-400 focus:outline-none"
                    />
                </Field>

                <div className="space-y-2">
                    <span className="text-xs font-semibold text-amber-700">位置情報（任意）</span>
                    <button
                        type="button"
                        onClick={fetchGps}
                        disabled={gpsLoading}
                        className="flex w-full items-center justify-center gap-2 rounded-lg border border-amber-300 bg-amber-50 py-2 text-sm text-amber-800 disabled:opacity-50"
                    >
                        {gpsLoading ? '取得中…' : '📍 現在地を取得'}
                    </button>
                    <div className="grid grid-cols-2 gap-2">
                        <label className="block">
                            <span className="text-xs text-gray-500">緯度</span>
                            <input
                                type="number" step="any" value={form.lat}
                                onChange={(e) => set('lat', e.target.value)}
                                placeholder="35.6895"
                                className="w-full rounded border border-amber-200 px-2 py-1 text-sm focus:outline-none"
                            />
                        </label>
                        <label className="block">
                            <span className="text-xs text-gray-500">経度</span>
                            <input
                                type="number" step="any" value={form.lng}
                                onChange={(e) => set('lng', e.target.value)}
                                placeholder="139.6917"
                                className="w-full rounded border border-amber-200 px-2 py-1 text-sm focus:outline-none"
                            />
                        </label>
                    </div>
                    {(errors.lat || errors.lng) && (
                        <p className="text-xs text-red-600">{errors.lat ?? errors.lng}</p>
                    )}
                </div>

                <Field label="訪問日 *" error={errors.visited_at}>
                    <input
                        type="date"
                        value={form.visited_at}
                        onChange={(e) => set('visited_at', e.target.value)}
                        className="w-full rounded-lg border border-amber-200 px-3 py-2 text-base focus:outline-none"
                    />
                </Field>

                <div className="space-y-2">
                    <div className="flex items-center justify-between">
                        <span className="text-xs font-semibold text-amber-700">本文（Markdown）</span>
                        <button
                            type="button"
                            onClick={() => fileInputRef.current?.click()}
                            disabled={imageUploading}
                            className="flex items-center gap-1 rounded border border-amber-300 bg-amber-50 px-2 py-1 text-xs text-amber-800 disabled:opacity-50"
                        >
                            {imageUploading ? '⏳ アップロード中…' : '🖼 画像を挿入'}
                        </button>
                        <input
                            ref={fileInputRef}
                            type="file"
                            accept="image/*"
                            className="hidden"
                            onChange={handleFileChange}
                        />
                    </div>
                    <textarea
                        ref={textareaRef}
                        value={form.body}
                        onChange={(e) => set('body', e.target.value)}
                        rows={18}
                        placeholder={'Markdownで記事を書いてください\n\n## 見出し\n**太字** / *斜体*\n\n- リスト\n\n画像は「画像を挿入」ボタンからアップロードできます'}
                        className="w-full rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 font-mono text-sm leading-relaxed focus:border-amber-400 focus:outline-none"
                    />
                    {errors.body && <p className="text-xs text-red-600">{errors.body}</p>}
                </div>

                <div className="flex gap-3 pt-2">
                    <button
                        type="button"
                        onClick={(e) => submit(e, false)}
                        disabled={processing}
                        className="flex-1 rounded-full border border-amber-400 py-3 text-sm font-semibold text-amber-700 disabled:opacity-50"
                    >
                        下書き保存
                    </button>
                    <button
                        type="submit"
                        onClick={(e) => submit(e, true)}
                        disabled={processing}
                        className="flex-1 rounded-full bg-amber-600 py-3 text-sm font-semibold text-white disabled:opacity-50"
                    >
                        {isEdit ? '更新する' : '投稿する'}
                    </button>
                </div>
            </form>
        </WebLayout>
    )
})

const Field = ({
    label, children, error,
}: { label: string; children: React.ReactNode; error?: string }) => (
    <label className="block">
        <span className="text-xs font-semibold text-amber-700">{label}</span>
        <div className="mt-1">{children}</div>
        {error && <p className="mt-1 text-xs text-red-600">{error}</p>}
    </label>
)

export default PostCreatePage
