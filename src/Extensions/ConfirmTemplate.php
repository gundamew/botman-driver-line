<?php

namespace BotMan\Drivers\Line\Extensions;

class ConfirmTemplate extends AbstractTemplate
{
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
}
