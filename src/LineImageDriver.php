<?php

namespace BotMan\Drivers\Line;

use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class LineImageDriver extends LineDriver
{
    const DRIVER_NAME = 'LineImage';

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
            $this->messages = [new IncomingMessage(
                Image::PATTERN,
                $this->getMessageSender($this->event->get('source')),
                '',
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
        $endpoint = str_replace(
            '{messageId}',
            urlencode($this->event->get('message')['id']),
            '/message/{messageId}/content'
        );

        return $this->http->get($this->getApiUrl($endpoint), [], [
            'Authorization: Bearer '.$this->config->get('channel_access_token'),
        ])->getContent();
    }
}
