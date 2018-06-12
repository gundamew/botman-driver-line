<?php

namespace BotMan\Drivers\Line;

use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class LineMessageLocationDriver extends LineDriver
{
    const DRIVER_NAME = 'LineMessageLocation';

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest()
    {
        return ($this->event->get('type') === 'message')
            && ($this->event->get('message')['type'] === 'location')
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
            $message = new IncomingMessage(
                Location::PATTERN,
                $this->event->get('source')['userId'],
                $this->event->get('source')['userId'],
                $this->payload
            );

            $message->setLocation(new Location(
                $this->event->get('message')['latitude'],
                $this->event->get('message')['longitude']
            ));

            $this->messages = [$message];
        }

        return $this->messages;
    }
}
