<?php

namespace BotMan\Drivers\Line\Extensions\Templates\Actions;

class PostbackButton extends AbstractButton
{
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => 'postback',
            'label' => $this->label,
            'data' => $this->value,
            'displayText' => $this->label,
        ];
    }
}
