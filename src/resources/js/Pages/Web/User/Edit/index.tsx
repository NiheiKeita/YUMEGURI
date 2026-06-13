import React from 'react'
import WebLayout from '@/Layouts/WebLayout'
import InputError from '@/Components/InputError'
import InputLabel from '@/Components/InputLabel'
import TextInput from '@/Components/TextInput'
import Button from '@/Components/Button'
import { Link } from '@inertiajs/react'
import { useUserProfileEdit } from './hooks'

type Profile = {
    id: number
    name: string
    email: string
    tel: string | null
}

type Props = {
    profile: Profile
}

export const UserEdit = React.memo<Props>(function UserEdit({ profile }) {
    const { data, setData, submit, processing, errors } = useUserProfileEdit(profile)

    return (
        <WebLayout>
            <div className="px-4 py-6">
                <div className="flex items-center justify-between">
                    <div>
                        <p className="text-xs uppercase tracking-[0.4em] text-amber-700">Profile</p>
                        <h1 className="font-yuGothic text-2xl font-bold text-gray-900">プロフィール編集</h1>
                    </div>
                    <Link
                        href={route('web.users.show', profile.id)}
                        className="rounded-full border border-amber-700 px-4 py-2 text-sm text-amber-800"
                    >
                        戻る
                    </Link>
                </div>

                <form onSubmit={submit} className="mt-8 space-y-5 rounded-lg border border-amber-100 bg-white p-5 shadow-sm">
                    <div>
                        <InputLabel htmlFor="name" value="名前" />
                        <TextInput
                            id="name"
                            name="name"
                            value={data.name}
                            className="mt-1 block w-full"
                            autoComplete="name"
                            onChange={(e) => setData('name', e.target.value)}
                            required
                        />
                        <InputError message={errors.name} className="mt-2" />
                    </div>

                    <div>
                        <InputLabel htmlFor="email" value="メールアドレス" />
                        <TextInput
                            id="email"
                            type="email"
                            name="email"
                            value={data.email}
                            className="mt-1 block w-full"
                            autoComplete="email"
                            onChange={(e) => setData('email', e.target.value)}
                            required
                        />
                        <InputError message={errors.email} className="mt-2" />
                    </div>

                    <div>
                        <InputLabel htmlFor="tel" value="電話番号" />
                        <TextInput
                            id="tel"
                            type="tel"
                            name="tel"
                            value={data.tel}
                            className="mt-1 block w-full"
                            autoComplete="tel"
                            onChange={(e) => setData('tel', e.target.value)}
                        />
                        <InputError message={errors.tel} className="mt-2" />
                    </div>

                    <Button type="submit" variant="blue" className="w-full" disabled={processing}>
                        更新する
                    </Button>
                </form>
            </div>
        </WebLayout>
    )
})

export default UserEdit
