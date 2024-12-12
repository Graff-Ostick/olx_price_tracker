<?php
namespace Repositories;

use PDO;

class SubscriptionRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $url
     * @param string $email
     * @param string $token
     * @return bool
     */
    public function addSubscription(string $url, string $email, string $token): bool
    {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO subscriptions (url, email, token, is_verified) VALUES (:url, :email, :token, 0)'
            );
            return $stmt->execute(['url' => $url, 'email' => $email, 'token' => $token]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getSubscriptions(): array
    {
        try {
            $stmt = $this->db->query('SELECT * FROM subscriptions');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage());
        }
    }

    /**
     * @param int $subscriptionId
     * @param float $price
     * @param string $currency
     * @return bool
     */
    public function updatePriceAndCurrency(int $subscriptionId, float $price, string $currency): bool
    {
        try {
            $stmt = $this->db->prepare('
                UPDATE subscriptions SET last_price = :price, currency = :currency WHERE id = :id
            ');
            return $stmt->execute(['price' => $price, 'currency' => $currency, 'id' => $subscriptionId]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage());
        }
    }

    /**
     * @param string $token
     * @return array
     */
    public function getSubscriptionByToken(string $token): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM subscriptions WHERE token = :token LIMIT 1");
            $stmt->execute(['token' => $token]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (\PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage());
        }
    }

    public function setVerifiedById(int $id): void
    {
        try {
            $stmt = $this->db->prepare('UPDATE subscriptions SET is_verified = 1 WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage());
        }
    }

}
