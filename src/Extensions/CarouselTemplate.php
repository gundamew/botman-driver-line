<?php

namespace BotMan\Drivers\Line\Extensions;

class CarouselTemplate extends AbstractTemplate
{
    /** @var array */
    protected $columns = [];

    /** @var string */
    protected $imageAspectRatio = 'rectangle';

    /** @var string */
    protected $imageSize = 'cover';

    /**
     * @param CarouselColumn $column
     * @return $this
     */
    public function addColumn(CarouselColumn $column)
    {
        $this->columns[] = $column->toArray();

        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function addColumns(array $columns)
    {
        foreach ($columns as $column) {
            $this->columns[] = $column->toArray();
        }

        return $this;
    }

    /**
     * Select column image aspect ratio.
     * Available value: rectangle, square
     *
     * @param string $aspectRatio
     * @return $this
     */
    public function imageAspectRatio($aspectRatio)
    {
        $this->imageAspectRatio = $aspectRatio;

        return $this;
    }

    /**
     * Select column image size.
     * Available value: cover, contain
     *
     * @param string $size
     * @return $this
     */
    public function imageSize($size)
    {
        $this->imageSize = $size;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $message = [
            'type' => 'template',
            'altText' => $this->text,
            'template' => [
                'type' => 'carousel',
                'columns' => $this->columns,
            ],
        ];

        foreach ($this->columns as $column) {
            if (array_key_exists('thumbnailImageUrl', $column)) {
                $message['template']['imageAspectRatio'] = $this->imageAspectRatio;
                $message['template']['imageSize'] = $this->imageSize;
                break;
            }
        }

        return $message;
    }
}
