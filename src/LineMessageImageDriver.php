<?php

namespace BotMan\Drivers\Line;

use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class LineMessageImageDriver extends LineDriver
{
    const DRIVER_NAME = 'LineMessageImage';

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest()
    {
        return ($this->event->get('type') === 'message')
            && ($this->event->get('message')['type'] === 'image')
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
            $this->message = [new IncomingMessage(
                Image::PATTERN,
                $this->event->get('source')['userId'],
                $this->event->get('source')['userId'],
                $this->payload
            )];
        }

        return $this->messages;
    }

    /**
     * @return string
     */
    public function getImageContent()
    {
        return $this->getMessageContent($this->event->get('message')['id']);
    }
}
