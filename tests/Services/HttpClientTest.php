<?php

use PHPUnit\Framework\TestCase;
use Services\HttpClient;
use Monolog\Logger;

class HttpClientTest extends TestCase
{
    public function testGetRequest()
    {
        $logger = $this->createMock(Logger::class);
        $httpClient = new HttpClient($logger);

        $this->mockFunction('curl_exec', function($ch) {
            return '{"price": 100.5, "currency": "USD"}';
        });

        $result = $httpClient->get('https://example.com');

        $this->assertStringContainsString('price', $result);
    }

    private function mockFunction($functionName, callable $callback)
    {
        runkit7_function_rename($functionName, $functionName . '_original');
        runkit7_function_add($functionName, '', $callback);
    }
}
