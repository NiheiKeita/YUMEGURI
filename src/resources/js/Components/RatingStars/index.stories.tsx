import { Meta, StoryObj } from '@storybook/react-vite'
import { RatingStars } from '.'

const meta: Meta<typeof RatingStars> = {
    title: 'components/RatingStars',
    component: RatingStars,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Five: Story = { render: () => <RatingStars value={5} /> }
export const Three: Story = { render: () => <RatingStars value={3} /> }
export const Large: Story = { render: () => <RatingStars value={4} size="lg" /> }
