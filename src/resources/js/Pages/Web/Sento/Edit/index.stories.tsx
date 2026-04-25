import { Meta, StoryObj } from '@storybook/react-vite'
import { SentoEdit } from '.'

const meta: Meta<typeof SentoEdit> = {
    title: 'pages/Web/Sento/Edit',
    component: SentoEdit,
    tags: ['autodocs', '!test'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <SentoEdit
            sento={{
                id: 1, name: '湊湯', prefecture: '東京都', address: '...', lat: 35, lng: 139,
                has_shampoo: true, has_soap: true, status: 'open',
            }}
        />
    ),
}
