<?php

use PHPUnit\Framework\TestCase;
use Entities\Subscription;

class SubscriptionTest extends TestCase
{
    public function testSubscriptionCreation()
    {
        $subscription = new Subscription();
        $subscription->id = 1;
        $subscription->url = 'https://example.com';
        $subscription->email = 'my.flud.info@gmail.com';
        $subscription->lastPrice = 100.5;
        $subscription->currency = 'USD';
        $subscription->createdAt = '2024-12-10';

        $this->assertEquals(1, $subscription->id);
        $this->assertEquals('https://example.com', $subscription->url);
        $this->assertEquals('my.flud.info@gmail.com', $subscription->email);
        $this->assertEquals(100.5, $subscription->lastPrice);
        $this->assertEquals('USD', $subscription->currency);
        $this->assertEquals('2024-12-10', $subscription->createdAt);
    }
}
