import { Meta, StoryObj } from '@storybook/react-vite'
import { Carousel } from '.'

const meta: Meta<typeof Carousel> = {
    title: 'components/Carousel',
    component: Carousel,
    // '!test' で Vitest 実行から除外（react-slick の CJS interop が Vitest Browser Mode と相性が悪い既知問題）
    tags: ['autodocs', '!test'],
}
export default meta

type Story = StoryObj<typeof meta>

export const Default: Story = {
    render() {

        const images = [
            'https://img.minpaku-bukken.com/Rooms/16636/1022336548570766d1b37e86c72b9cc6.jpg',
            'https://img.minpaku-bukken.com/Rooms/16636/1022336548570766d1b37e86c72b9cc6.jpg',
            'https://img.minpaku-bukken.com/Rooms/16636/1022336548570766d1b37e86c72b9cc6.jpg',
            'https://img.minpaku-bukken.com/Rooms/16636/1022336548570766d1b37e86c72b9cc6.jpg',
        ]
        return (
            <Carousel images={images} />
        )
    },
}
