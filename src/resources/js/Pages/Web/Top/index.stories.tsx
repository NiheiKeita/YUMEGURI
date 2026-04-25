import { Meta, StoryObj } from '@storybook/react-vite'
import { Top } from '.'

const meta: Meta<typeof Top> = {
    title: 'pages/Web/Top',
    component: Top,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <Top
            latestReviews={[
                {
                    id: 1,
                    sento_id: 1,
                    visited_at: '2026-04-20',
                    rating: 5,
                    body: '炭酸泉が最高だった。サウナも90度で締まりがある。',
                    has_sauna: true,
                    sauna_temp: 90,
                    has_mizuburo: true,
                    mizuburo_temp: 18,
                    bath_types: ['炭酸泉'],
                    want_revisit: true,
                    crowding: 3,
                    best_time: '夕方',
                    sento: {
                        id: 1, name: '湊湯', prefecture: '東京都', address: '中央区',
                        lat: 35, lng: 139, has_shampoo: true, has_soap: true, status: 'open',
                    },
                },
            ]}
        />
    ),
}
export const Empty: Story = { render: () => <Top latestReviews={[]} /> }
