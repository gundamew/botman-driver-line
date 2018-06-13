<?php

namespace BotMan\Drivers\Line\Extensions;

class ButtonsTemplate extends AbstractTemplate
{
    /** @var string */
    protected $title = '';

    /** @var array */
    protected $defaultAction = [];

    /** @var string */
    protected $titleImageUrl = '';

    /** @var string */
    protected $titleImageShape = 'rectangle';

    /** @var string */
    protected $titleImageSize = 'cover';

    /** @var string */
    protected $titleImageBackgroundColor = '#ffffff';

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
     * @param AbstractButton $default
     * @return $this
     */
    public function defaultAction(AbstractButton $defaultAction)
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
    public function titleImageUrl($url)
    {
        $this->titleImageUrl = $url;

        return $this;
    }

    /**
     * Select title image aspect ratio.
     * Available value: rectangle, square
     *
     * @param string $shape
     * @return $this
     */
    public function titleImageShape($shape)
    {
        $this->titleImageShape = $shape;

        return $this;
    }

    /**
     * Select title image size.
     * Available value: cover, contain
     *
     * @param string $size
     * @return $this
     */
    public function titleImageSize($size)
    {
        $this->titleImageSize = $size;

        return $this;
    }

    /**
     * Specify RGB value of background color of title image.
     *
     * @param string $color
     * @return $this
     */
    public function titleImageBackgroundColor($color)
    {
        $this->titleImageBackgroundColor = $color;

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

        if (!empty($this->title)) {
            $message['template']['title'] = $this->title;
        }

        if (!empty($this->defaultAction)) {
            $message['template']['defaultAction'] = $this->defaultAction;
        }

        if (!empty($this->titleImageUrl)) {
            $message['template']['thumbnailImageUrl'] = $this->titleImageUrl;
            $message['template']['imageAspectRatio'] = $this->titleImageShape;
            $message['template']['imageSize'] = $this->titleImageSize;
            $message['template']['imageBackgroundColor'] = $this->titleImageBackgroundColor;
        }

        return $message;
    }
}
