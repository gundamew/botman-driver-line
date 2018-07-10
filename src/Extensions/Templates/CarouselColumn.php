<?php

namespace BotMan\Drivers\Line\Extensions\Templates;

use BotMan\Drivers\Line\Extensions\Templates\Actions\AbstractAction;

class CarouselColumn extends AbstractColumn
{
    /** @var array */
    protected $actions = [];

    /** @var string */
    protected $text = '';

    /** @var string */
    protected $title = '';

    /** @var string */
    protected $imageBackgroundColor = '#ffffff';

    /**
     * @param string $text
     *
     * @return static
     */
    public static function create($text)
    {
        return new static($text);
    }

    /**
     * @param string $text
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

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
     * @param AbstractAction $action
     * @return $this
     */
    public function addButton(AbstractAction $action)
    {
        $this->actions[] = $action->toArray();

        return $this;
    }

    /**
     * @param array $actions
     * @return $this
     */
    public function addButtons(array $actions)
    {
        foreach ($actions as $action) {
            $this->actions[] = $action->toArray();
        }

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

        if (! empty($this->title)) {
            $message['title'] = $this->title;
        }

        if (! empty($this->defaultAction)) {
            $message['defaultAction'] = $this->defaultAction;
        }

        if (! empty($this->imageUrl)) {
            $message['thumbnailImageUrl'] = $this->imageUrl;
            $message['imageBackgroundColor'] = $this->imageBackgroundColor;
        }

        return $message;
    }
}
