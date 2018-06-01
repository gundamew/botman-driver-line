<?php

namespace BotMan\Drivers\Line;

use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class LineMessageTextDriver extends LineDriver
{
    const DRIVER_NAME = 'LineMessageText';

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest()
    {
        return ($this->event->get('type') === 'message')
            && ($this->event->get('message')['type'] === 'text')
            && !empty($this->event->get('message')['text'])
            && $this->validateSignature();
    }

    /**
     * Retrieve the chat message.
     *
     * @return array
     */
    public function getMessages()
    {
        if (empty($this->messages)) {
            $this->messages = [new IncomingMessage(
                $this->event->get('message')['text'],
                $this->event->get('source')['userId'],
                $this->event->get('source')['userId'],
                $this->payload
            )];
        }

        return $this->messages;
    }
}
