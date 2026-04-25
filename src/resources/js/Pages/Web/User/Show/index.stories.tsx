import { Meta, StoryObj } from '@storybook/react-vite'
import { UserShow } from '.'

const meta: Meta<typeof UserShow> = {
    title: 'pages/Web/User/Show',
    component: UserShow,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <UserShow
            profile={{ id: 1, name: 'けいた' }}
            stats={{ visited_count: 12, prefecture_count: 3, city_count: 7 }}
            reviews={[
                {
                    id: 1, sento_id: 1, visited_at: '2026-04-20', rating: 5,
                    body: '', has_sauna: true, sauna_temp: 90, has_mizuburo: true, mizuburo_temp: 18,
                    bath_types: [], want_revisit: true, crowding: 3, best_time: '夕方',
                    sento: { id: 1, name: '湊湯', prefecture: '東京都', address: '', lat: 0, lng: 0,
                        has_shampoo: true, has_soap: true, status: 'open' },
                },
            ]}
        />
    ),
}
