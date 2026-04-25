import { Meta, StoryObj } from '@storybook/react-vite'
import { UserMap } from '.'

const meta: Meta<typeof UserMap> = {
    title: 'pages/Web/User/Map',
    component: UserMap,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <UserMap
            profile={{ id: 1, name: 'けいた' }}
            pins={[{ id: 1, name: '湊湯', lat: 35.67, lng: 139.77, visited_at: '2026-04-20', rating: 5 }]}
        />
    ),
}
