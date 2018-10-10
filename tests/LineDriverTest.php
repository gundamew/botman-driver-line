<?php

namespace Tests;

use BotMan\BotMan\Http\Curl;
use PHPUnit\Framework\TestCase;
use BotMan\Drivers\Line\LineDriver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class LineDriverTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected $config = [
        'line' => [
            'channel_access_token' => 'CHANNEL-ACCESS-TOKEN',
            'channel_secret' => 'CHANNEL-SECRET',
        ],
    ];

    public function tearDown()
    {
    }

    public function setUp()
    {
    }

    private function getRequest(array $responseData)
    {
        $request = \Mockery::mock(Request::class.'[getContent]');
        $request->shouldReceive('getContent')->andReturn(json_encode($responseData));

        return $request;
    }

    private function getDriver(array $responseData, $config = [], $signature = '', $htmlInterface = null)
    {
        $request = $this->getRequest($responseData);
        $request->headers->set('X_LINE_SIGNATURE', $signature);

        if ($htmlInterface === null) {
            $htmlInterface = \Mockery::mock(Curl::class);
        }

        return new LineDriver($request, $config, $htmlInterface);
    }

    /**
     * @test
     * @covers BotMan\Drivers\Line\LineDriver::getName
     */
    public function it_returns_the_driver_name()
    {
        $driver = $this->getDriver([]);
        $this->assertSame('Line', $driver->getName());
    }

    /**
     * @test
     * @covers BotMan\Drivers\Line\LineDriver::matchesRequest
     * @dataProvider validRequestDataProvider
     */
    public function it_matches_the_request($request, $signature)
    {
        $driver = $this->getDriver($request, $this->config, $signature);
        $this->assertTrue($driver->matchesRequest());
    }

    /**
     * @test
     * Tests the function split name
     */
    public function it_return_split_name()
    {
        $driver = $this->getDriver([]);
        $names = $driver->split_name('Foo Bar Baz');

        $this->assertEquals('Foo', $names['first_name']);
        $this->assertEquals('Bar', $names['middle_name']);
        $this->assertEquals('Baz', $names['last_name']);
    }

    public function validRequestDataProvider()
    {
        return [
            [
                [
                    'events' => [
                        [
                            'type' => 'postback',
                            'replyToken' => 'f70944fd79dd82c5410c94ef768902a5',
                            'timestamp' => 1494194288,
                            'source' => [
                                'type' => 'group',
                                'userId' => 'U31df07b7ebb4d8bdc6dc8ecff61d1ce6',
                                'groupId' => 'C6cd0d99c8557a06c588ca86634c5b540',
                            ],
                            'postback' => [
                                'data' => 'foo=bar',
                            ],
                        ],
                    ],
                ],
                'NVLIi2ocerv5MYH9azuEcQghbSwgDbNC8v4DyeLzgRc=',
            ],
            [
                [
                    'events' => [
                        [
                            'type' => 'message',
                            'replyToken' => 'ca40bc3284027ca4dae554b0bd79014c',
                            'timestamp' => 1058633985,
                            'source' => [
                                'type' => 'group',
                                'userId' => 'U537ca269c625fef8421a8b3be9c039cb',
                                'groupId' => 'C69bcaa18564a7d59c5131249023631a9',
                            ],
                            'message' => [
                                'type' => 'text',
                                'id' => '0624459666900',
                                'text' => 'foo',
                            ],
                        ],
                    ],
                ],
                'dOTa+L4wlC+jy++2VtYN6NglG/DkHhX8N2bWJR3y4h4=',
            ],
        ];
    }

    /**
     * @test
     * @covers BotMan\Drivers\Line\LineDriver::matchesRequest
     * @dataProvider invalidRequestDataProvider
     */
    public function it_matches_the_request_not($request, $signature)
    {
        $driver = $this->getDriver($request, $this->config, $signature);
        $this->assertFalse($driver->matchesRequest());
    }

    public function invalidRequestDataProvider()
    {
        return [
            [
                [
                    'events' => [
                        [
                            'type' => 'postback',
                            'replyToken' => '19cac073298af6e5783fda3370b3f7c3',
                            'timestamp' => 141291325,
                            'source' => [
                                'type' => 'room',
                                'userId' => 'U8ddbc2e699fbd9b8515461b469dc8200',
                                'roomId' => 'Re98f9e083ca70c4aea23e4bfc0c08461',
                            ],
                            'postback' => [
                                'data' => 'foo=bar',
                            ],
                        ],
                    ],
                ],
                'foo',
            ],
            [
                [
                    'events' => [
                        [
                            'type' => 'bar',
                            'replyToken' => 'e77362343f48513630b7b662dd7fda45',
                            'timestamp' => 110269731,
                            'source' => [
                                'type' => 'group',
                                'userId' => 'Ued067c1f46c18bff78580b3aa56b954f',
                                'groupId' => 'Ca8dff735be31bee0caaf2fb01ecae561',
                            ],
                        ],
                    ],
                ],
                'aoIFisQA5rlzdSFKDdhHuSs5oiiun76vlUzhzYObKbw=',
            ],
        ];
    }

    /**
     * @test
     * @covers BotMan\Drivers\Line\LineDriver::isConfigured
     */
    public function it_is_configured()
    {
        $driver = $this->getDriver([]);
        $this->assertFalse($driver->isConfigured());
    }

    /**
     * @test
     * @covers BotMan\Drivers\Line\LineDriver::isConfigured
     */
    public function it_is_configured_not()
    {
        $driver = $this->getDriver([], $this->config);
        $this->assertTrue($driver->isConfigured());
    }

    /**
     * @test
     * @covers BotMan\Drivers\Line\LineDriver::sendPayload
     * @dataProvider validRequestDataProvider
     */
    public function it_can_reply_string_messages($request, $signature)
    {
        $htmlInterface = \Mockery::mock(Curl::class);
        $htmlInterface->shouldReceive('post')
            ->with('https://api.line.me/v2/bot/message/reply', [], [
                'replyToken' => $request['events'][0]['replyToken'],
                'messages' => [
                    [
                        'type' => 'text',
                        'text' => 'Test',
                    ],
                ],
            ], [
                'Authorization: Bearer '.$this->config['line']['channel_access_token'],
                'Content-Type: application/json',
            ], true)
            ->once()
            ->andReturn(new Response());

        $driver = $this->getDriver($request, $this->config, $signature, $htmlInterface);
        $driver->sendPayload($driver->buildServicePayload(
            new OutgoingMessage('Test'), $driver->getMessages()[0]
        ));
    }
}
