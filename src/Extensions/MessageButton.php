<?php

namespace BotMan\Drivers\Line\Extensions;

class MessageButton extends ActionObject
{
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => 'message',
            'label' => $this->label,
            'text' => $this->value,
        ];
    }
}
