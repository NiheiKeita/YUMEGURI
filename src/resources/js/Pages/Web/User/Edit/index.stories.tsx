import { Meta, StoryObj } from '@storybook/react-vite'
import { UserEdit } from '.'

const meta: Meta<typeof UserEdit> = {
    title: 'pages/Web/User/Edit',
    component: UserEdit,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <UserEdit
            profile={{
                id: 1,
                name: 'けいた',
                email: 'keita@example.com',
                tel: '09012345678',
            }}
        />
    ),
}
