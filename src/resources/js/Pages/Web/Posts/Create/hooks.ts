import { router } from '@inertiajs/react'
import { useRef, useState, type FormEvent } from 'react'
import type { Post } from '@/types/yumeguri'

type Form = {
    title: string
    place_name: string
    lat: string
    lng: string
    visited_at: string
    published_at: string
    body: string
}

const today = () => new Date().toISOString().slice(0, 10)

const fromPost = (post: Post | null): Form => ({
    title: post?.title ?? '',
    place_name: post?.place_name ?? '',
    lat: post?.lat != null ? String(post.lat) : '',
    lng: post?.lng != null ? String(post.lng) : '',
    visited_at: post?.visited_at ?? today(),
    published_at: post?.published_at ? post.published_at.slice(0, 10) : today(),
    body: post?.body ?? '',
})

export const usePostForm = (post: Post | null) => {
    const [form, setForm] = useState<Form>(fromPost(post))
    const [processing, setProcessing] = useState(false)
    const [errors, setErrors] = useState<Partial<Record<string, string>>>({})
    const [gpsLoading, setGpsLoading] = useState(false)
    const [imageUploading, setImageUploading] = useState(false)
    const textareaRef = useRef<HTMLTextAreaElement>(null)

    const set = <K extends keyof Form>(key: K, value: Form[K]) => {
        setForm(f => ({ ...f, [key]: value }))
    }

    const fetchGps = () => {
        if (!navigator.geolocation) return
        setGpsLoading(true)
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                setForm(f => ({
                    ...f,
                    lat: String(pos.coords.latitude.toFixed(7)),
                    lng: String(pos.coords.longitude.toFixed(7)),
                }))
                setGpsLoading(false)
            },
            () => setGpsLoading(false),
        )
    }

    const insertAtCursor = (text: string) => {
        const ta = textareaRef.current
        if (!ta) {
            setForm(f => ({ ...f, body: f.body + text }))
            return
        }
        const start = ta.selectionStart
        const end = ta.selectionEnd
        const before = ta.value.slice(0, start)
        const after = ta.value.slice(end)
        const newBody = before + text + after
        const newCursor = start + text.length
        setForm(f => ({ ...f, body: newBody }))
        setTimeout(() => {
            ta.setSelectionRange(newCursor, newCursor)
            ta.focus()
        }, 0)
    }

    const uploadImage = async (file: File) => {
        setImageUploading(true)
        const fd = new FormData()
        fd.append('image', file)
        try {
            const res = await fetch('/posts/images/upload', {
                method: 'POST',
                body: fd,
                credentials: 'include',
            })
            if (res.ok) {
                const json = (await res.json()) as { path: string; url: string }
                const alt = file.name.replace(/\.[^.]+$/, '')
                insertAtCursor(`\n![${alt}](${json.url})\n`)
            }
        } finally {
            setImageUploading(false)
        }
    }

    const submit = (e: FormEvent, publish: boolean) => {
        e.preventDefault()
        setProcessing(true)
        setErrors({})

        const payload = {
            title: form.title,
            place_name: form.place_name || null,
            lat: form.lat !== '' ? Number(form.lat) : null,
            lng: form.lng !== '' ? Number(form.lng) : null,
            body: form.body || null,
            visited_at: form.visited_at,
            published_at: publish ? form.published_at : null,
        }

        const url = post ? `/posts/${post.id}` : '/posts'
        const method = post ? 'patch' : 'post'

        router[method](url, payload, {
            onError: (errs) => { setErrors(errs); setProcessing(false) },
            onFinish: () => setProcessing(false),
        })
    }

    return {
        form, set, errors, processing, gpsLoading, imageUploading,
        textareaRef, fetchGps, uploadImage, submit,
    }
}
