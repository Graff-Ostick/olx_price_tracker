<?php

use PHPUnit\Framework\TestCase;
use Repositories\SubscriptionRepository;

class SubscriptionRepositoryTest extends TestCase
{
    private $pdo;
    private $repo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->repo = new SubscriptionRepository($this->pdo);
    }

    public function testAddSubscription()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $this->pdo->method('prepare')->willReturn($stmt);
        $stmt->method('execute')->willReturn(true);

        $result = $this->repo->addSubscription('https://example.com', 'test@example.com');

        $this->assertTrue($result);
    }

    public function testUpdatePriceAndCurrency()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $this->pdo->method('prepare')->willReturn($stmt);
        $stmt->method('execute')->willReturn(true);

        $result = $this->repo->updatePriceAndCurrency(1, 150.5, 'USD');

        $this->assertTrue($result);
    }
}
