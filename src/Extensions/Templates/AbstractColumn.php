<?php

namespace BotMan\Drivers\Line\Extensions\Templates;

use BotMan\Drivers\Line\Extensions\Templates\Actions\AbstractAction;

abstract class AbstractColumn implements \JsonSerializable
{
    /** @var string */
    protected $imageUrl = '';

    /** @var array */
    protected $defaultAction = [];

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
     * Set action when column image is tapped.
     *
     * @param AbstractAction $action
     * @return $this
     */
    public function defaultAction(AbstractAction $action)
    {
        $this->defaultAction = $action->toArray();

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
