<?php

namespace BotMan\Drivers\Line\Extensions\Templates;

class ImageCarousel extends AbstractTemplate
{
    /** @var array */
    protected $columns = [];

    /**
     * @param ImageCarouselColumn $column
     * @return $this
     */
    public function addColumn(ImageCarouselColumn $column)
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
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => 'template',
            'altText' => $this->text,
            'template' => [
                'type' => 'image_carousel',
                'columns' => $this->columns,
            ],
        ];
    }
}
