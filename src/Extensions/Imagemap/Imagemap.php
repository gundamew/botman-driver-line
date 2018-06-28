<?php

namespace BotMan\Drivers\Line\Extensions\Imagemap;

use BotMan\Drivers\Line\Extensions\Imagemap\Actions\AbstractAction;

class Imagemap implements \JsonSerializable
{
    /** @var array */
    protected $actions = [];

    /** @var string */
    protected $text = '';

    /** @var string */
    protected $baseUrl = '';

    /** @var int */
    protected $width = 0;

    /** @var int */
    protected $height = 0;

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
     * @param AbstractAction $action
     * @return $this
     */
    public function addAction(AbstractAction $action)
    {
        $this->actions[] = $action->toArray();

        return $this;
    }

    /**
     * @param array $actions
     * @return $this
     */
    public function addActions(array $actions)
    {
        foreach ($actions as $action) {
            $this->actions[] = $action->toArray();
        }

        return $this;
    }

    /**
     * @param string $baseUrl
     * @return $this
     */
    public function baseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * @param int $width
     * @return $this
     */
    public function width($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @param int $height
     * @return $this
     */
    public function height($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => 'imagemap',
            'altText' => $this->text,
            'baseUrl' => $this->baseUrl,
            'baseSize' => [
                'width' => $this->width,
                'height' => $this->height,
            ],
            'actions' => $this->actions,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
