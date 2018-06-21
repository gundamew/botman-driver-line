<?php

namespace BotMan\Drivers\Line\Extensions;

class CarouselColumn extends AbstractTemplate
{
    /** @var string */
    protected $title = '';

    /** @var array */
    protected $defaultAction = [];

    /** @var string */
    protected $imageUrl = '';

    /** @var string */
    protected $imageBackgroundColor = '#ffffff';

    /**
     * Set the title of column.
     *
     * @param string $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set action when column image is tapped.
     *
     * @param AbstractButton $default
     * @return $this
     */
    public function defaultAction(AbstractButton $defaultAction)
    {
        $this->defaultAction = $defaultAction->toArray();

        return $this;
    }

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
     * Specify RGB value of background color of column image.
     *
     * @param string $color
     * @return $this
     */
    public function imageBackgroundColor($color)
    {
        $this->imageBackgroundColor = $color;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $message = [
            'text' => $this->text,
            'actions' => $this->actions,
        ];

        if (!empty($this->title)) {
            $message['title'] = $this->title;
        }

        if (!empty($this->defaultAction)) {
            $message['defaultAction'] = $this->defaultAction;
        }

        if (!empty($this->imageUrl)) {
            $message['thumbnailImageUrl'] = $this->imageUrl;
            $message['imageBackgroundColor'] = $this->imageBackgroundColor;
        }

        return $message;
    }
}
