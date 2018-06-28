<?php

namespace BotMan\Drivers\Line\Extensions\Imagemap\Actions;

use BotMan\Drivers\Line\Extensions\Imagemap\Area as ImagemapArea;

abstract class AbstractAction implements \JsonSerializable
{
    /** @var string */
    protected $label = '';

    /** @var string */
    protected $value = '';

    /** @var array */
    protected $area = [];

    /**
     * @param string $label
     *
     * @return static
     */
    public static function create($label)
    {
        return new static($label);
    }

    /**
     * @param string $label
     */
    public function __construct($label)
    {
        $this->label = $label;
    }

    /**
     * Set button value.
     *
     * @param string $value
     * @return $this
     */
    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set tappable area.
     *
     * @param ImagemapArea $area
     * @return $this
     */
    public function area(ImagemapArea $value)
    {
        $this->area = $area->toArray();

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
