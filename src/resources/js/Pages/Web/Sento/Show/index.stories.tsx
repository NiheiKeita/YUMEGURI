import { Meta, StoryObj } from '@storybook/react-vite'
import { SentoShow } from '.'

const meta: Meta<typeof SentoShow> = {
    title: 'pages/Web/Sento/Show',
    component: SentoShow,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <SentoShow
            sento={{
                id: 1, name: '湊湯', name_kana: 'みなとゆ', prefecture: '東京都', city: '中央区',
                address: '東京都中央区湊1-6-2', lat: 35.6712, lng: 139.7763,
                phone: '03-0000-0000', hours: '15:00-23:00', closed_days: '月曜', price: 520,
                nearest_station: '八丁堀駅', walk_minutes: 5, has_shampoo: true, has_soap: true,
                status: 'open',
                reviews: [
                    {
                        id: 1, sento_id: 1, user: { id: 1, name: 'けいた' }, visited_at: '2026-04-20',
                        rating: 5, body: '炭酸泉が最高だった', has_sauna: true, sauna_temp: 90,
                        has_mizuburo: true, mizuburo_temp: 18, bath_types: ['炭酸泉'], want_revisit: true,
                        crowding: 3, best_time: '夕方',
                    },
                ],
            }}
        />
    ),
}
