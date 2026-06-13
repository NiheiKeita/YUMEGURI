import { useForm } from '@inertiajs/react'
import type { FormEvent } from 'react'
import type { SentoDetail } from '@/types/yumeguri'

type ProposeForm = {
    changes: Record<string, string>
    reason: string
}

export const useProposeForm = (sento: SentoDetail) => {
    const { data, setData, post, processing, errors } = useForm<ProposeForm>({
        changes: {},
        reason: '',
    })

    const setChange = (key: string, value: string) => {
        const next = { ...data.changes }
        if (value === '') {
            delete next[key]
        } else {
            next[key] = value
        }
        setData('changes', next)
    }

    const setReason = (v: string) => setData('reason', v)

    const submit = (e: FormEvent) => {
        e.preventDefault()
        post(`/sentos/${sento.id}/propose`)
    }

    return { data, setChange, setReason, submit, processing, errors }
}
