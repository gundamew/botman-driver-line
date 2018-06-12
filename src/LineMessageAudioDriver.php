<?php

namespace BotMan\Drivers\Line;

use BotMan\BotMan\Messages\Attachments\Audio;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class LineMessageAudioDriver extends LineDriver
{
    const DRIVER_NAME = 'LineMessageAudio';

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest()
    {
        return ($this->event->get('type') === 'message')
            && ($this->event->get('message')['type'] === 'audio')
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
                Audio::PATTERN,
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
    public function getAudioContent()
    {
        $endpoint = str_replace(
            '{messageId}',
            urlencode($this->event->get('message')['id']),
            '/message/{messageId}/content'
        );

        return $this->http->get($this->getApiUrl($endpoint), [], [
            'Authorization: Bearer ' . $this->config->get('channel_access_token'),
        ])->getContent();
    }
}
