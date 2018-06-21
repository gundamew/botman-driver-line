<?php

namespace BotMan\Drivers\Line\Extensions;

class ImageCarouselColumn extends AbstractTemplate
{
    /** @var string */
    protected $imageUrl = '';

    /** @var array */
    protected $action = [];

    /**
     * Set column image URL.
     *
     * @param string $url
     * @return $this
     */
    public function imageUrl($url)
    {
        $this->imageUrl = $url;

        return $this;
    }

    /**
     * @param AbstractButton $button
     * @return $this
     */
    public function addButton(AbstractButton $button)
    {
        $this->action = $button->toArray();

        return $this;
    }

    /**
     * @param array $buttons
     * @return $this
     */
    public function addButtons(array $buttons)
    {
        // The column object only contains one action object
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'imageUrl' => $this->imageUrl,
            'action' => $this->action,
        ];
    }
}
