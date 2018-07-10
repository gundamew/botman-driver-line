<?php

namespace BotMan\Drivers\Line\Extensions\Templates;

class Buttons extends AbstractTemplate
{
    /** @var string */
    protected $title = '';

    /** @var array */
    protected $defaultAction = [];

    /** @var string */
    protected $imageUrl = '';

    /** @var string */
    protected $imageAspectRatio = 'rectangle';

    /** @var string */
    protected $imageSize = 'cover';

    /** @var string */
    protected $imageBackgroundColor = '#ffffff';

    /**
     * Set the title of buttons template message.
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
     * Set action when image is tapped.
     *
     * @param AbstractAction $default
     * @return $this
     */
    public function defaultAction(AbstractAction $defaultAction)
    {
        $this->defaultAction = $defaultAction->toArray();

        return $this;
    }

    /**
     * Set title image URL of template.
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
     * Select title image aspect ratio.
     * Available value: rectangle, square.
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
     * Select title image size.
     * Available value: cover, contain.
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
     * Specify RGB value of background color of title image.
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
            'type' => 'template',
            'altText' => $this->text,
            'template' => [
                'type' => 'buttons',
                'text' => $this->text,
                'actions' => $this->actions,
            ],
        ];

        if (! empty($this->title)) {
            $message['template']['title'] = $this->title;
        }

        if (! empty($this->defaultAction)) {
            $message['template']['defaultAction'] = $this->defaultAction;
        }

        if (! empty($this->imageUrl)) {
            $message['template']['thumbnailImageUrl'] = $this->imageUrl;
            $message['template']['imageAspectRatio'] = $this->imageAspectRatio;
            $message['template']['imageSize'] = $this->imageSize;
            $message['template']['imageBackgroundColor'] = $this->imageBackgroundColor;
        }

        return $message;
    }
}
