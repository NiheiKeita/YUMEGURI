import { Meta, StoryObj } from '@storybook/react-vite'
import { FlashMessage } from '.'

const meta: Meta<typeof FlashMessage> = {
    title: 'components/FlashMessage',
    component: FlashMessage,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render() {
        return (
            <FlashMessage>defaultTitle</FlashMessage>
        )
    },
}
