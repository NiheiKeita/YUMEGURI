import { Meta, StoryObj } from '@storybook/react-vite'
import { ProposalsIndex } from '.'

const meta: Meta<typeof ProposalsIndex> = {
    title: 'pages/Admin/Proposals/Index',
    component: ProposalsIndex,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <ProposalsIndex
            proposals={{
                data: [
                    {
                        id: 1, sento_id: 1, sento: { id: 1, name: '湊湯' },
                        proposer: { id: 2, name: 'ともだち' },
                        changes: { hours: '14:00-23:30' },
                        reason: '営業時間が変わったみたい',
                        status: 'pending',
                        reviewed_at: null,
                        created_at: '2026-04-20T10:00:00+09:00',
                    },
                ],
                links: { first: '', last: '', prev: null, next: null },
                meta: { current_page: 1, from: 1, last_page: 1, per_page: 30, to: 1, total: 1 },
            }}
        />
    ),
}
