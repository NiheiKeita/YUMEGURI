import { Meta, StoryObj } from '@storybook/react-vite'
import { Nearby } from '.'

const meta: Meta<typeof Nearby> = {
    title: 'pages/Web/Nearby',
    component: Nearby,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <Nearby
            origin={{ lat: 35.68, lng: 139.77 }}
            sentos={[
                {
                    id: 1, name: '湊湯', prefecture: '東京都', address: '...',
                    lat: 35.67, lng: 139.77, has_shampoo: true, has_soap: true, status: 'open',
                    distance_km: 0.42, nearest_station: '八丁堀', walk_minutes: 5,
                },
            ]}
        />
    ),
}
