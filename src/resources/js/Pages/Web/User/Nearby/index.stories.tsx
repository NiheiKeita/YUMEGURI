import { Meta, StoryObj } from '@storybook/react-vite'
import { UserNearby } from '.'

const meta: Meta<typeof UserNearby> = {
    title: 'pages/Web/User/Nearby',
    component: UserNearby,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <UserNearby
            profile={{ id: 1, name: 'けいた' }}
            origin={{ lat: 35.68, lng: 139.77 }}
            sentos={[]}
        />
    ),
}
