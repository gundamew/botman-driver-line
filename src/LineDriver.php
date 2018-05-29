<?php

namespace BotMan\Drivers\Line;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class LineDriver extends HttpDriver
{
    const DRIVER_NAME = 'Line';

    const MESSAGING_API_URL = 'https://api.line.me/v2/bot/message/reply';

    protected $messages = [];

    /**
     * @param Request $request
     */
    public function buildPayload(Request $request)
    {
        $this->payload = new ParameterBag((array) json_decode($request->getContent(), true));
        $this->event = Collection::make((array) $this->payload->get('events')[0]);
        $this->signature = $request->headers->get('X_LINE_SIGNATURE', '');
        $this->content = $request->getContent();
        $this->config = Collection::make($this->config->get('line', []));
    }

    /**
     * @param IncomingMessage $matchingMessage
     * @return \BotMan\BotMan\Users\User
     */
    public function getUser(IncomingMessage $matchingMessage)
    {
    }

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest()
    {
        return (empty($this->config->get('channel_secret')) || $this->validateSignature());
    }

    /**
     * @param  IncomingMessage $message
     * @return \BotMan\BotMan\Messages\Incoming\Answer
     */
    public function getConversationAnswer(IncomingMessage $message)
    {
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
                $this->event->get('replyToken'),
                $this->event->get('source')['userId'],
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
    public function buildServicePayload($message)
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
        return $this->http->post(static::MESSAGING_API_URL, [], $payload, [
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
}
