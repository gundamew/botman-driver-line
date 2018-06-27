<?php

namespace BotMan\Drivers\Line;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

use BotMan\BotMan\Drivers\HttpDriver;
use BotMan\BotMan\Drivers\Events\GenericEvent;
use BotMan\BotMan\Messages\Attachments\Audio;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Attachments\Video;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Users\User;

use BotMan\Drivers\Line\Events\Follow;
use BotMan\Drivers\Line\Events\Join;
use BotMan\Drivers\Line\Events\Leave;
use BotMan\Drivers\Line\Events\Postback;
use BotMan\Drivers\Line\Events\Unfollow;

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

    /**
     * Low-level method to perform driver specific API requests.
     *
     * @param string $endpoint
     * @param array $parameters
     * @param \BotMan\BotMan\Messages\Incoming\IncomingMessage $matchingMessage
     * @return void
     */
    public function sendRequest($endpoint, array $parameters, IncomingMessage $matchingMessage)
    {
        $parameters = [
            'replyToken' => $this->event->get('replyToken'),
            'messages' => [$parameters],
        ];

        return $this->http->post($this->getApiUrl($endpoint), [], $parameters, [
            'Authorization: Bearer ' . $this->config->get('channel_access_token'),
            'Content-Type: application/json',
        ], true);
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
        return ($this->validateSignature()
            && in_array($this->event->get('type'), ['message', 'postback'], true));
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
     * @return bool|DriverEventInterface
     */
    public function hasMatchingEvent()
    {
        switch ($this->event->get('type')) {
            case 'follow':
                return new Follow($this->event->get('source'));
                break;

            case 'unfollow':
                return new Unfollow($this->event->get('source'));
                break;

            case 'join':
                return new Join($this->event->get('source'));
                break;

            case 'leave':
                return new Leave($this->event->get('source'));
                break;

            case 'postback':
                return new Postback($this->event->get('postback'));
                break;

            default:
                return false;
                break;
        }

        return false;
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
                '',
                $this->getMessageSender($this->event->get('source')),
                '',
                $this->payload
            );

            if (isset($this->event->get('message')['text'])) {
                $message->setText($this->event->get('message')['text']);
            } elseif (isset($this->event->get('postback')['data'])) {
                $message->setText($this->event->get('postback')['data']);
            }

            $this->messages = [$message];
        }

        return $this->messages;
    }

    /**
     * @param array $source
     * @return string
     */
    protected function getMessageSender(array $source)
    {
        if (!in_array($source['type'], ['user', 'group', 'room'], true)) {
            return '';
        }

        return $source[$source['type'] . 'Id'];
    }

    /**
     * @param Question|OutgoingMessage $message
     * @param IncomingMessage $matchingMessage
     * @param array $additionalParameters
     * @return array
     */
    public function buildServicePayload($message, $matchingMessage, $additionalParameters = [])
    {
        $parameters = [
            'replyToken' => $this->event->get('replyToken'),
            'messages' => [],
        ];

        if ($message instanceof OutgoingMessage) {
            $parameters['messages'][] = [
                'type' => 'text',
                'text' => $message->getText(),
            ];

            $attachment = $message->getAttachment();

            if ($attachment !== null) {
                if ($attachment instanceof Image) {
                    $parameters['messages'][] = [
                        'type' => 'image',
                        'originalContentUrl' => $attachment->getUrl(),
                        'previewImageUrl' => $attachment->getUrl(),
                    ];
                } elseif ($attachment instanceof Video) {
                    $parameters['messages'][] = [
                        'type' => 'video',
                        'originalContentUrl' => $attachment->getUrl(),
                        'previewImageUrl' => $attachment->getExtras('thumbnail'),
                    ];
                } elseif ($attachment instanceof Audio) {
                    $parameters['messages'][] = [
                        'type' => 'audio',
                        'originalContentUrl' => $attachment->getUrl(),
                        'duration' => $attachment->getExtras('duration'),
                    ];
                } elseif ($attachment instanceof Location) {
                    $parameters['messages'][] = [
                        'type' => 'location',
                        'title' => $attachment->getExtras('title'),
                        'address' => $attachment->getExtras('address'),
                        'latitude' => $attachment->getLatitude(),
                        'longitude' => $attachment->getLongitude(),
                    ];
                }
            }
        } else {
            $parameters['messages'][] = $message;
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
