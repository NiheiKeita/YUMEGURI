import { Meta, StoryObj } from '@storybook/react-vite'
import { SentoIndex } from '.'

const meta: Meta<typeof SentoIndex> = {
    title: 'pages/Web/Sento/Index',
    component: SentoIndex,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <SentoIndex
            filters={{}}
            sentos={{
                data: [
                    {
                        id: 1, name: '湊湯', prefecture: '東京都', city: '中央区',
                        address: '...', lat: 35, lng: 139, has_shampoo: true, has_soap: true,
                        status: 'open', avg_rating: 4.5, nearest_station: '八丁堀', walk_minutes: 5,
                    },
                ],
                links: { first: '', last: '', prev: null, next: null },
                meta: { current_page: 1, from: 1, last_page: 1, per_page: 20, to: 1, total: 1 },
            }}
        />
    ),
}
