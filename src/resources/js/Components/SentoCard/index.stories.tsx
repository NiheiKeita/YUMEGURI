import { Meta, StoryObj } from '@storybook/react-vite'
import { SentoCard } from '.'
import type { SentoSummary } from '@/types/yumeguri'

const meta: Meta<typeof SentoCard> = {
    title: 'components/SentoCard',
    component: SentoCard,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

const base: SentoSummary = {
    id: 1,
    name: '湊湯',
    prefecture: '東京都',
    city: '中央区',
    address: '東京都中央区湊1-6-2',
    lat: 35.6712,
    lng: 139.7763,
    nearest_station: '八丁堀駅',
    walk_minutes: 5,
    has_shampoo: true,
    has_soap: true,
    status: 'open',
    avg_rating: 4.5,
    review_count: 12,
}

export const Default: Story = { render: () => <div className="max-w-sm"><SentoCard sento={base} /></div> }
export const WithDistance: Story = {
    render: () => (
        <div className="max-w-sm">
            <SentoCard sento={{ ...base, distance_km: 1.234 }} />
        </div>
    ),
}
