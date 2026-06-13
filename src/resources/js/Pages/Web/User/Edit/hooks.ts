import { useForm } from '@inertiajs/react'
import type { FormEvent } from 'react'

type Profile = {
    id: number
    name: string
    email: string
    tel: string | null
}

type Form = {
    name: string
    email: string
    tel: string
}

export const useUserProfileEdit = (profile: Profile) => {
    const { data, setData, patch, processing, errors } = useForm<Form>({
        name: profile.name,
        email: profile.email,
        tel: profile.tel ?? '',
    })

    const submit = (e: FormEvent) => {
        e.preventDefault()
        patch(route('web.users.update', profile.id), { preserveScroll: true })
    }

    return { data, setData, submit, processing, errors }
}
