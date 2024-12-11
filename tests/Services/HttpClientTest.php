<?php

use PHPUnit\Framework\TestCase;
use Services\HttpClient;
use Monolog\Logger;

class HttpClientTest extends TestCase
{
    public function testGetRequest()
    {
        $loggerMock = $this->createMock(Logger::class);
        $httpClientMock = $this->getMockBuilder(HttpClient::class)
            ->setConstructorArgs([$loggerMock])
            ->onlyMethods(['curlExecWrapper'])
            ->getMock();

        $httpClientMock->method('curlExecWrapper')
            ->willReturn('{"price": 100.5, "currency": "USD"}');

        $result = $httpClientMock->get('https://example.com');

        $this->assertStringContainsString('price', $result);
        $this->assertStringContainsString('currency', $result);
    }

    public function testGetRequestThrowsRuntimeExceptionOnError()
    {
        $loggerMock = $this->createMock(Logger::class);

        $loggerMock->expects($this->once())
            ->method('error')
            ->with($this->stringContains('CURL request returned false'));

        $httpClientMock = $this->getMockBuilder(HttpClient::class)
            ->setConstructorArgs([$loggerMock])
            ->onlyMethods(['curlExecWrapper'])
            ->getMock();

        $httpClientMock->method('curlExecWrapper')
            ->willReturn(false);

        $this->expectException(\RuntimeException::class);
        $httpClientMock->get('https://example.com');
    }
}
