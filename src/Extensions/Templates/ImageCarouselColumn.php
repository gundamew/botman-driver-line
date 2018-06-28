<?php

namespace BotMan\Drivers\Line\Extensions\Templates;

class ImageCarouselColumn extends AbstractColumn
{
    /**
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'imageUrl' => $this->imageUrl,
            'action' => $this->defaultAction,
        ];
    }
}
