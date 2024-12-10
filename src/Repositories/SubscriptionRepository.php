<?php
namespace Repositories;

use PDO;

class SubscriptionRepository {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function addSubscription(string $url, string $email): bool {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO subscriptions (url, email) VALUES (:url, :email)'
            );
            return $stmt->execute(['url' => $url, 'email' => $email]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage());
        }
    }

    public function getSubscriptions(): array {
        try {
            $stmt = $this->db->query('SELECT * FROM subscriptions');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage());
        }
    }

    public function updatePriceAndCurrency(int $subscriptionId, float $price, string $currency): bool {
        try {
            $stmt = $this->db->prepare('
                UPDATE subscriptions SET last_price = :price, currency = :currency WHERE id = :id
            ');
            return $stmt->execute(['price' => $price, 'currency' => $currency, 'id' => $subscriptionId]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage());
        }
    }
}
