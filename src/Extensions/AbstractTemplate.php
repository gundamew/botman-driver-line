<?php

namespace BotMan\Drivers\Line\Extensions;

abstract class AbstractTemplate implements \JsonSerializable
{
    /** @var array */
    protected $actions = [];

    /** @var string */
    protected $text = '';

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
     * @param AbstractButton $button
     * @return $this
     */
    public function addButton(AbstractButton $button)
    {
        $this->actions[] = $button->toArray();

        return $this;
    }

    /**
     * @param array $buttons
     * @return $this
     */
    public function addButtons(array $buttons)
    {
        foreach ($buttons as $button) {
            $this->actions[] = $button->toArray();
        }

        return $this;
    }

    /**
     * @return array
     */
    abstract public function toArray();

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
