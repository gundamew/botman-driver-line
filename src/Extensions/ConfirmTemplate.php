<?php

namespace BotMan\Drivers\Line\Extensions;

class ConfirmTemplate implements \JsonSerializable
{
    /** @var array */
    protected $actions;

    /** @var string */
    protected $text;

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
        $this->actions = [];
    }

    /**
     * @param MessageButton $button
     * @return $this
     */
    public function addButton(MessageButton $button)
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
    public function toArray()
    {
        return [
            'type' => 'template',
            'altText' => $this->text,
            'template' => [
                'type' => 'confirm',
                'text' => $this->text,
                'actions' => $this->actions,
            ],
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
