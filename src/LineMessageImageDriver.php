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
        return $this->http->get(
            $this->getApiUrl('/message/' . urlencode($this->event->get('message')['id']) . '/content'),
            [],
            [
                'Authorization: Bearer ' . $this->config->get('channel_access_token'),
            ]
        )->getContent();
    }
}
