import { Meta, StoryObj } from '@storybook/react-vite'
import { SentoPropose } from '.'

const meta: Meta<typeof SentoPropose> = {
    title: 'pages/Web/Sento/Propose',
    component: SentoPropose,
    tags: ['autodocs', '!test'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <SentoPropose
            sento={{
                id: 1, name: '湊湯', prefecture: '東京都', address: '東京都中央区湊1-6-2',
                lat: 35, lng: 139, has_shampoo: true, has_soap: true, status: 'open',
            }}
        />
    ),
}
