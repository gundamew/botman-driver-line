<?php

namespace BotMan\Drivers\Line\Extensions;

class UriButton extends ActionObject
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
