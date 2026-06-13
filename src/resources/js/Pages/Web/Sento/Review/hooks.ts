import { useForm } from '@inertiajs/react'
import type { FormEvent } from 'react'
import type { SentoReview } from '@/types/yumeguri'

type Form = {
    visited_at: string
    rating: number
    body: string
    has_sauna: boolean
    sauna_temp: number | null
    has_mizuburo: boolean
    mizuburo_temp: number | null
    bath_types: string[]
    want_revisit: boolean
    crowding: number | null
    best_time: string | null
}

const today = () => new Date().toISOString().slice(0, 10)

const fromReview = (r: SentoReview | null): Form => ({
    visited_at: r?.visited_at ?? today(),
    rating: r?.rating ?? 4,
    body: r?.body ?? '',
    has_sauna: r?.has_sauna ?? false,
    sauna_temp: r?.sauna_temp ?? null,
    has_mizuburo: r?.has_mizuburo ?? false,
    mizuburo_temp: r?.mizuburo_temp ?? null,
    bath_types: r?.bath_types ?? [],
    want_revisit: r?.want_revisit ?? true,
    crowding: r?.crowding ?? null,
    best_time: r?.best_time ?? null,
})

export const useReviewForm = (sentoId: number, initial: SentoReview | null) => {
    const { data, setData, post, processing, errors } = useForm<Form>(fromReview(initial))

    const set = <K extends keyof Form>(key: K, value: Form[K]) => {
        setData(current => ({ ...current, [key]: value }))
    }

    const submit = (e: FormEvent) => {
        e.preventDefault()
        post(`/sentos/${sentoId}/review`, { preserveScroll: true })
    }

    return { data, set, submit, processing, errors }
}
