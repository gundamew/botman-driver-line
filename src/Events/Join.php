<?php

namespace BotMan\Drivers\Line\Events;

class Join extends AbstractEvent
{
    /**
     * Return the event name to match.
     *
     * @return string
     */
    public function getName()
    {
        return 'join';
    }
}
