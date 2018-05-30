<?php

namespace BotMan\Drivers\Line;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

use BotMan\BotMan\Drivers\HttpDriver;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Users\User;

class LineDriver extends HttpDriver
{
    const DRIVER_NAME = 'Line';

    const API_URL_BASE = 'https://api.line.me/v2/bot';

    protected $messages = [];

    /**
     * @param Request $request
     */
    public function buildPayload(Request $request)
    {
        $this->payload = $request->request->all();
        $this->event = Collection::make(json_decode($this->content, true)['events'][0]);
        $this->signature = $request->headers->get('X_LINE_SIGNATURE', '');
        $this->config = Collection::make($this->config->get('line'));
    }

    public function sendRequest($endpoint, array $parameters, IncomingMessage $matchingMessage)
    {
        return $this->http->post($this->getApiUrl($endpoint), [], $parameters);
    }

    /**
     * @param IncomingMessage $matchingMessage
     * @return \BotMan\BotMan\Users\User
     */
    public function getUser(IncomingMessage $matchingMessage)
    {
        return new User();
    }

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest()
    {
        return $this->validateSignature();
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
     * @return array
     */
    public function buildServicePayload($message, $matchingMessage, $additionalParameters = [])
    {
        $parameters = array_merge_recursive([
            'replyToken' => $this->event->get('replyToken'),
            'messages' => [
                [
                    'type' => 'text',
                    'text' => '',
                ],
            ],
        ], $additionalParameters);

        if ($message instanceof Question || $message instanceof OutgoingMessage) {
            $parameters['messages'][0]['text'] = $message->getText();
        } else {
            $parameters['messages'][0]['text'] = $message;
        }

        return $parameters;
    }

    /**
     * @param array $payload
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

    /**
     * @return bool
     */
    protected function validateSignature()
    {
        return hash_equals(
            base64_encode(
                hash_hmac('sha256', $this->content, $this->config->get('channel_secret'), true)
            ),
            $this->signature
        );
    }

    /**
     * Generate the LINE messaging API URL for the given endpoint.
     *
     * @param string $endpoint
     * @return string
     */
    protected function getApiUrl($endpoint)
    {
        return static::API_URL_BASE . $endpoint;
    }
}
