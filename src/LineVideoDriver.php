<?php

namespace BotMan\Drivers\Line;

use BotMan\BotMan\Messages\Attachments\Video;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class LineVideoDriver extends LineDriver
{
    const DRIVER_NAME = 'LineVideo';

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest()
    {
        return ($this->event->get('type') === 'message')
            && ($this->event->get('message')['type'] === 'video')
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
                Video::PATTERN,
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
    public function getVideoContent()
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
