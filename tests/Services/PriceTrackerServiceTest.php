<?php

use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Services\EmailService;
use Services\PriceTrackerService;
use Repositories\SubscriptionRepository;
use Services\HttpClient;

class PriceTrackerServiceTest extends TestCase
{
    public function testFetchCurrentPrice()
    {

        $repo = $this->createMock(SubscriptionRepository::class);
        $httpClient = $this->createMock(HttpClient::class);
        $logger = $this->createMock(Logger::class);
        $emailService = $this->createMock(EmailService::class);

        $priceTrackerService = new PriceTrackerService($repo, $httpClient,$emailService, $logger);

        $httpClient->method('get')->willReturn('{"price": 100.5, "currency": "USD"}');

        $result = $priceTrackerService->fetchCurrentPrice('https://example.com');

        $this->assertEquals(100.5, $result['price']);
        $this->assertEquals('USD', $result['currency']);
    }
}
