<?php

namespace BotMan\Drivers\Line\Extensions\Imagemap\Actions;

class UriButton extends AbstractButton
{
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => 'uri',
            'label' => $this->label,
            'linkUri' => $this->value,
            'area' => $this->area,
        ];
    }
}
