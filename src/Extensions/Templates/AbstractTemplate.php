<?php

namespace BotMan\Drivers\Line\Extensions\Templates;

use BotMan\Drivers\Line\Extensions\Templates\Actions\AbstractAction;

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
    abstract public function toArray();

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
