<?php

namespace BotMan\Drivers\Line\Extensions\Templates\Actions;

class UriButton extends AbstractAction
{
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => 'uri',
            'label' => $this->label,
            'uri' => $this->value,
        ];
    }
}
