<?php

namespace BotMan\Drivers\Line\Events;

class Postback extends AbstractEvent
{
    /**
     * Return the event name to match.
     *
     * @return string
     */
    public function getName()
    {
        return 'postback';
    }
}
