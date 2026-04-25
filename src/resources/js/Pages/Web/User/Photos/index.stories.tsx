import { Meta, StoryObj } from '@storybook/react-vite'
import { UserPhotos } from '.'

const meta: Meta<typeof UserPhotos> = {
    title: 'pages/Web/User/Photos',
    component: UserPhotos,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <UserPhotos
            profile={{ id: 1, name: 'けいた' }}
            photos={[
                { id: 1, sento_id: 1, review_id: 1, url: null, category: 'exterior', caption: '雪の湊湯' },
            ]}
        />
    ),
}
