<?php

namespace BotMan\Drivers\Line\Extensions\Imagemap;

class Area implements \JsonSerializable
{
    /** @var int */
    protected $x = '';

    /** @var int */
    protected $y = '';

    /** @var int */
    protected $width = '';

    /** @var int */
    protected $height = '';

    /**
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     *
     * @return static
     */
    public static function create($x, $y, $width, $height)
    {
        return new static($x, $y, $width, $height);
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     */
    public function __construct($x, $y, $width, $height)
    {
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'width' => $this->width,
            'height' => $this->height,
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
