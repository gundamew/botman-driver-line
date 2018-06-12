<?php

namespace BotMan\Drivers\Line\Extensions;

class MessageButton extends AbstractButton
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
