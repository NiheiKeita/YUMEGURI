import { Meta, StoryObj } from '@storybook/react-vite'
import { Map } from '.'

const meta: Meta<typeof Map> = {
    title: 'pages/Web/Map',
    component: Map,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <Map
            pins={[
                { id: 1, name: '湊湯', lat: 35.67, lng: 139.77, visited: true },
                { id: 2, name: '清水湯', lat: 35.66, lng: 139.7, visited: false },
            ]}
        />
    ),
}
