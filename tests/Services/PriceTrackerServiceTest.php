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

        $httpClient->method('get')->willReturn('"priceCurrency":"USD","price":100.5/');

        $result = $priceTrackerService->fetchCurrentPrice('https://www.olx.ua/d/uk/obyavlenie/akumulyatorna-lampa-liberty-9-w-4100k-opt-IDVTG2f.html?reason=hp%7Cpromoted');

        $this->assertEquals(100.5, $result['price']);
        $this->assertEquals('USD', $result['currency']);
    }
}
