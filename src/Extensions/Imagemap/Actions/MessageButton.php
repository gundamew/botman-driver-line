<?php

namespace BotMan\Drivers\Line\Extensions\Imagemap\Actions;

class MessageButton extends AbstractAction
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
            'area' => $this->area,
        ];
    }
}
