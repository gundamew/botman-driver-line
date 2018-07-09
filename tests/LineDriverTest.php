<?php

namespace Tests;

use Mockery as m;
use BotMan\BotMan\Http\Curl;
use BotMan\Drivers\Line\LineDriver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class LineDriverTest extends TestCase
{
    protected $config = [
        'line' => [
            'channel_access_token' => 'CHANNEL_ACCESS_TOKEN',
            'channel_secret' => 'CHANNEL_SECRET',
        ],
    ];

    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        parent::setUp();
    }

    private function getRequest(array $responseData)
    {
        $request = m::mock(Request::class.'[getContent]');
        $request->shouldReceive('getContent')->andReturn(json_encode($responseData));

        return $request;
    }

    private function getDriver(array $responseData, $config = [], $signature = '', $htmlInterface = null)
    {
        $request = $this->getRequest($responseData);
        $request->headers->set('X_LINE_SIGNATURE', $signature);

        if ($htmlInterface === null) {
            $htmlInterface = m::mock(Curl::class);
        }

        return new LineDriver($request, $config, $htmlInterface);
    }

    /** @test */
    public function it_returns_the_driver_name()
    {
        $driver = $this->getDriver([]);
        $this->assertSame('Line', $driver->getName());
    }
}
