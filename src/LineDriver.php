<?php

namespace BotMan\Drivers\Line;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

use BotMan\BotMan\Drivers\HttpDriver;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Users\User;

class LineDriver extends HttpDriver
{
    const DRIVER_NAME = 'Line';

    const API_URL_BASE = 'https://api.line.me/v2/bot';

    protected $messages = [];

    protected $eventTypes = [
        'message',
        'follow',
        'unfollow',
    ];

    protected $messageTypes = [
        'text',
        'image',
        'video',
        'audio',
        'file',
        'location',
        'sticker',
    ];

    /**
     * @param Request $request
     */
    public function buildPayload(Request $request)
    {
        $this->payload = $request->request->all();
        $this->event = Collection::make(json_decode($this->content, true)['events'][0]);
        $this->signature = $request->headers->get('X_LINE_SIGNATURE', '');
    }

    public function sendRequest($endpoint, array $parameters, IncomingMessage $matchingMessage)
    {
        return $this->http->post($this->getApiUrl($endpoint), [], $parameters, [
            'Authorization: Bearer ' . $this->config->get('channel_access_token'),
        ], true);
    }

    /**
     * @param IncomingMessage $matchingMessage
     * @return \BotMan\BotMan\Users\User
     */
    public function getUser(IncomingMessage $matchingMessage)
    {
        $userId = $matchingMessage->getSender();

        $response = $this->sendRequest('/profile/' . urlencode($userId), [], $matchingMessage);
        $profile = Collection::make(json_decode($response->getContent(), true));

        return new User($profile->get('userId'), null, null, $profile->get('displayName'), $profile->toJson());
    }

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest()
    {
        $isValidSignature = !empty($this->config->get('channel_secret')) || $this->validateSignature();
        $isValidMessage = ($this->event->get('type') === 'message') && (in_array($this->event->get('message')['type'], $this->messageTypes, true));

        return ($isValidSignature && $isValidMessage);
    }

    /**
     * @param  IncomingMessage $message
     * @return \BotMan\BotMan\Messages\Incoming\Answer
     */
    public function getConversationAnswer(IncomingMessage $message)
    {
        return Answer::create($message->getText())->setMessage($message);
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
                $this->event->get('replyToken'),
                $this->payload
            )];
        }

        return $this->messages;
    }

    /**
     * @param string|Question|OutgoingMessage $message
     * @param IncomingMessage $matchingMessage
     * @param array $additionalParameters
     * @return Response
     */
    public function buildServicePayload($message, $matchingMessage, $additionalParameters = [])
    {
        $text = ($message instanceof OutgoingMessage) ? $message->getText() : $message;

        return [
            'replyToken' => $this->event->get('replyToken'),
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $text,
                ],
            ],
        ];
    }

    /**
     * @param mixed $payload
     * @return Response
     */
    public function sendPayload($payload)
    {
        return $this->http->post($this->getApiUrl('/message/reply'), [], $payload, [
            'Authorization: Bearer ' . $this->config->get('channel_access_token'),
            'Content-Type: application/json',
        ], true);
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        return (!empty($this->config->get('channel_access_token')));
    }

    protected function validateSignature()
    {
        return hash_equals(
            base64_encode(
                hash_hmac('sha256', $this->content, $this->config->get('channel_secret'), true)
            ),
            $this->signature
        );
    }

    protected function getApiUrl($endpoint)
    {
        return static::API_URL_BASE . $endpoint;
    }
}
