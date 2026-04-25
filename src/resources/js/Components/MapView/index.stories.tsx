import { Meta, StoryObj } from '@storybook/react-vite'
import { MapView } from '.'

const meta: Meta<typeof MapView> = {
    title: 'components/MapView',
    component: MapView,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render: () => (
        <MapView
            pins={[
                { id: 1, name: '湊湯', lat: 35.67, lng: 139.77, visited: true },
                { id: 2, name: '清水湯', lat: 35.66, lng: 139.7, visited: false },
            ]}
            center={{ lat: 35.6812, lng: 139.7671 }}
        />
    ),
}
