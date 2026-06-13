import { Meta, StoryObj } from '@storybook/react-vite'
import { EquipmentChip } from '.'

const meta: Meta<typeof EquipmentChip> = {
    title: 'components/EquipmentChip',
    component: EquipmentChip,
    tags: ['autodocs'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Sauna: Story = { render: () => <EquipmentChip variant="sauna" /> }
export const Mizuburo: Story = { render: () => <EquipmentChip variant="mizuburo" /> }
export const Row: Story = {
    render: () => (
        <div className="flex flex-wrap gap-2">
            <EquipmentChip variant="sauna" />
            <EquipmentChip variant="mizuburo" />
            <EquipmentChip variant="tansan" />
            <EquipmentChip variant="kusuri" />
            <EquipmentChip variant="silk" />
            <EquipmentChip variant="denki" />
            <EquipmentChip variant="shampoo" />
            <EquipmentChip variant="soap" />
        </div>
    ),
}
