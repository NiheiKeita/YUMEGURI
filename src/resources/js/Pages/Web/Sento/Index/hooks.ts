import { router } from '@inertiajs/react'
import { useState } from 'react'
import type { SentoIndexFilters } from './index'

export const useSentoIndex = (initial: SentoIndexFilters) => {
    const [pending, setPending] = useState<SentoIndexFilters>(initial)

    const set = <K extends keyof SentoIndexFilters>(key: K, value: SentoIndexFilters[K]) => {
        setPending((prev) => ({ ...prev, [key]: value }))
    }

    const apply = () => {
        // 空の値は URL から除く（ブックマーク可能性を維持）
        const params: Record<string, string> = {}
        Object.entries(pending).forEach(([k, v]) => {
            if (v === undefined || v === '' || v === false) return
            params[k] = String(v)
        })
        router.get('/sentos', params, { preserveState: true, preserveScroll: true })
    }

    return { pending, set, apply }
}
