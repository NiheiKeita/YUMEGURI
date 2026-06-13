import { Meta, StoryObj } from '@storybook/react-vite'
import { SectionTitle } from '.'

const meta: Meta<typeof SectionTitle> = {
    title: 'components/SectionTitle',
    component: SectionTitle,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = { render: () => <SectionTitle sub="LATEST">最新の訪問記録</SectionTitle> }
